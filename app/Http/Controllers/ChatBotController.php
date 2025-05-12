<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
            // Determinar qué prompt usar basado en la autenticación
            $isAuthenticated = Auth::check();
            $systemPrompt = $isAuthenticated
                ? file_get_contents(base_path('prompts/assistactes_admin.txt'))
                : file_get_contents(base_path('prompts/assistactes_public.txt'));

            // Preparar la estructura de mensajes que enviaremos a la API
            $messages = [];

            // Si hay mensajes previos, los agregamos pero eliminamos cualquier mensaje del sistema
            // y limpiamos duplicados
            if (!empty($validated['messages']) && is_array($validated['messages'])) {
                // Filtrar mensajes de sistema anteriores - solo mantener mensajes de usuario y asistente
                $filteredMessages = array_filter($validated['messages'], function ($msg) {
                    return isset($msg['role']) && $msg['role'] !== 'system';
                });

                // Eliminar duplicados de los mensajes filtrados
                $messages = $this->removeDuplicateMessages(array_values($filteredMessages));
            }

            // Añadir el mensaje actual del usuario (solo si no es duplicado del último)
            $userMessage = [
                'role' => 'user',
                'content' => $validated['message']
            ];

            // Verificar que el último mensaje no sea idéntico al que se está enviando
            $lastMessage = end($messages);
            if (
                !$lastMessage ||
                $lastMessage['role'] !== 'user' ||
                $lastMessage['content'] !== $validated['message']
            ) {
                $messages[] = $userMessage;
            }

            // Siempre agregamos el mensaje del sistema actual al principio
            $messageStack = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                ...$messages
            ];

            $payload = [
                'model' => $this->model,
                'messages' => $messageStack,
                'stream' => false,
                'knowledge' => [
                    [
                        'type' => 'collection',
                        'id' => $isAuthenticated ? env('CHATBOT_COLLECTION_NAME_ADMIN') : env('CHATBOT_COLLECTION_NAME_USER')
                    ]
                ],
                'tool_ids' => $isAuthenticated ? ['consultar_user'] : ['consultar_actos'],
                'tool_choice' => 'required',
                'temperature' => 1,
                'top_p' => 1,
                'max_tokens' => 1000,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
                'seed' => 42,
                'stop' => null,

            ];
           // dd($payload);

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

                // Estructura de mensajes que mantendremos en el contexto (incluye el system actual)
                $contextMessages = [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    ...$messages
                ];

                return response()->json([
                    'response' => $messageContent,
                    'messages' => $contextMessages // Devolver todo el contexto actualizado
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
            if (
                $lastMessage &&
                $lastMessage['role'] === $message['role'] &&
                $lastMessage['content'] === $message['content']
            ) {
                // Si es duplicado, omitir
                continue;
            }

            // Agregar mensaje al resultado y actualizar referencia
            $result[] = $message;
            $lastMessage = $message;
        }

        return $result;
    }

    /**
     * Reinicia el chat y devuelve el prompt adecuado según estado de autenticación
     */
    public function resetChat(Request $request): JsonResponse
    {
        try {
            // Determinar qué prompt usar basado en la autenticación
            $isAuthenticated = Auth::check();
            $systemPrompt = $isAuthenticated
                ? file_get_contents(base_path('prompts/assistactes_admin.txt'))
                : file_get_contents(base_path('prompts/assistactes_public.txt'));

            // Crear un mensaje de bienvenida según el tipo de usuario
            $welcomeMessage = $isAuthenticated
                ? 'Benvingut/da a l\'àrea d\'administració. En què puc ajudar-te avui?'
                : 'Hola! Com puc ajudar-te amb la consulta d\'actes o inscripcions?';

            // Estructura de mensajes inicial que mantendremos en el contexto
            $contextMessages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'assistant',
                    'content' => $welcomeMessage
                ]
            ];

            return response()->json([
                'response' => $welcomeMessage,
                'messages' => $contextMessages,
                'isAuthenticated' => $isAuthenticated
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting chat', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error al reiniciar el chat',
            ], 500);
        }
    }
}
