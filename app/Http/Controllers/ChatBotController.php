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
            
            // Si hay mensajes previos, los agregamos primero
            if (!empty($validated['messages']) && is_array($validated['messages'])) {
                $messages = $validated['messages'];
            }
            
            // Añadir el mensaje actual del usuario
            $messages[] = [
                'role' => 'user',
                'content' => $validated['message']
            ];
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => false
            ]);
            
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
} 