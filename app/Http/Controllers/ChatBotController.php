<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    private string $apiUrl;
    private string $apiToken;
    private string $model;
    
    public function __construct()
    {
        $this->apiUrl = env('CHATBOT_API_URL');
        $this->apiToken = env('CHATBOT_API_TOKEN');
        $this->model = env('CHATBOT_MODEL');
    }
    
    /**
     * Process a chatbot message and return the response
     */
    public function processMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'sessionId' => 'required|string',
            'messages' => 'sometimes|array', // Historial de mensajes previos
        ]);
        
        try {
            // Preparar la estructura de mensajes que enviaremos a la API
            $messages = [];
            
            // Si hay mensajes previos, los agregamos primero pero eliminando duplicados
            if (!empty($validated['messages']) && is_array($validated['messages'])) {
                $messages = $this->removeDuplicateMessages($validated['messages']);
            }
            
            // Añadir el mensaje actual del usuario (solo si no es duplicado del último)
            $userMessage = [
                'role' => 'user',
                'content' => $validated['message']
            ];
            
            // Verificar que el último mensaje no sea idéntico al que se está enviando
            $lastMessage = end($messages);
            if (!$lastMessage || 
                $lastMessage['role'] !== 'user' || 
                $lastMessage['content'] !== $validated['message']) {
                $messages[] = $userMessage;
            }
            
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => false,
                'knowledge' => [
                    [
                        'type' => 'collection',
                        'id' => '47da7a1e-1eb1-4789-a566-dff63f0ff9e9'
                    ]
                ],
                'tool_ids' => ['consultar_actos']
            ];
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $payload);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Extraer el contenido del mensaje del formato de respuesta
                $messageContent = 'No s\'ha rebut cap resposta';
                $assistantMessage = [];
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $messageContent = $responseData['choices'][0]['message']['content'];
                    
                    // Crear el mensaje del asistente en formato estándar
                    $assistantMessage = [
                        'role' => 'assistant',
                        'content' => $messageContent
                    ];
                    
                    // Añadir este mensaje a la lista de mensajes para mantener el contexto
                    $messages[] = $assistantMessage;
                }
                
                return response()->json([
                    'response' => $messageContent,
                    'messages' => $messages // Devolver todo el contexto actualizado
                ]);
            }
            
            Log::error('ChatBot API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return response()->json([
                'error' => 'Error de comunicació amb el servei de xat',
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('ChatBot exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'S\'ha produït un error inesperat',
            ], 500);
        }
    }
    
    /**
     * Elimina mensajes duplicados consecutivos de un array de mensajes
     * 
     * @param array $messages Array de mensajes
     * @return array Array de mensajes sin duplicados consecutivos
     */
    private function removeDuplicateMessages(array $messages): array
    {
        $result = [];
        $lastMessage = null;
        
        foreach ($messages as $message) {
            // Verificar si el mensaje actual es igual al anterior
            if ($lastMessage && 
                $lastMessage['role'] === $message['role'] && 
                $lastMessage['content'] === $message['content']) {
                // Si es duplicado, omitir
                continue;
            }
            
            // Agregar mensaje al resultado y actualizar referencia
            $result[] = $message;
            $lastMessage = $message;
        }
        
        return $result;
    }
} 