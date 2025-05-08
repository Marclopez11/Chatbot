# Chatbot con Memoria de Contexto

Este proyecto implementa un chatbot interactivo con memoria de contexto utilizando Laravel para el backend y Vue.js para el frontend. El chatbot proporciona una interfaz de usuario intuitiva y mantiene el historial completo de la conversación para ofrecer respuestas contextualizadas.

## Características

- 🧠 **Memoria de contexto completa**: Mantiene el historial de conversación para respuestas más coherentes
- 🌐 **Interfaz reactiva**: Construida con Vue.js para una experiencia fluida
- 🔄 **Carga progresiva de mensajes**: Carga mensajes antiguos bajo demanda
- 💾 **Persistencia de datos**: Almacena conversaciones en localStorage
- 🎨 **Diseño moderno y responsive**: Adaptado a dispositivos móviles y escritorio
- 📱 **Experiencia de chat inmersiva**: Similar a aplicaciones populares de mensajería

## Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- Laravel 11
- Base de datos compatible con Laravel (MySQL, PostgreSQL, etc.)
- Acceso a un servicio de API de chat compatible (OpenAI, Ollama, etc.)

## Instalación

1. Clonar el repositorio:
   ```bash
   git clone [url-repositorio]
   cd chatbot
   ```

2. Instalar dependencias de PHP:
   ```bash
   composer install
   ```

3. Instalar dependencias de JavaScript:
   ```bash
   npm install
   ```

4. Copiar el archivo de entorno:
   ```bash
   cp .env.example .env
   ```

5. Generar clave de la aplicación:
   ```bash
   php artisan key:generate
   ```

6. Configurar las variables de entorno en el archivo `.env`:
   ```
   CHATBOT_API_URL=http://your-chat-api-url/api/chat/completions
   CHATBOT_API_TOKEN=your-api-token
   CHATBOT_MODEL=your-model-name
   ```

7. Compilar los assets:
   ```bash
   npm run dev
   ```

8. Iniciar el servidor:
   ```bash
   php artisan serve
   ```

## Configuración

### Variables de entorno

- `CHATBOT_API_URL`: URL de la API del servicio de chat (ejemplo: OpenAI, Ollama)
- `CHATBOT_API_TOKEN`: Token de autenticación para la API
- `CHATBOT_MODEL`: Nombre del modelo a utilizar (ejemplo: gpt-4, devlocl, etc.)

## Estructura del Proyecto

### Backend (Laravel)

- **Controllers**: Gestiona las solicitudes y respuestas
  - `ChatBotController.php`: Procesa los mensajes y mantiene el contexto

### Frontend (Vue.js)

- **Componentes**: 
  - `ChatBot.vue`: Componente principal que muestra la interfaz de chat
  
- **Stores**:
  - `chatStore.js`: Store Pinia que gestiona el estado y la lógica del chat

## Funcionamiento

El chatbot funciona de la siguiente manera:

1. El usuario escribe un mensaje en la interfaz
2. El mensaje se envía al backend junto con el historial de contexto
3. El backend se comunica con la API de chat configurada
4. La API procesa el mensaje y devuelve una respuesta
5. La respuesta se muestra en la interfaz y se almacena en el contexto
6. El contexto se actualiza para futuras interacciones

### Formato de mensajes

```json
{
  "model": "nombre-del-modelo",
  "messages": [
    { "role": "system", "content": "Instrucciones del sistema" },
    { "role": "user", "content": "Mensaje del usuario" },
    { "role": "assistant", "content": "Respuesta del asistente" },
    { "role": "user", "content": "Nuevo mensaje" }
  ],
  "stream": false
}
```

## API Endpoints

- **POST** `/api/chatbot/message`: Procesa un mensaje y devuelve la respuesta
  - Parámetros:
    - `message`: Texto del mensaje (requerido)
    - `sessionId`: ID de la sesión (requerido)
    - `messages`: Array con el historial de la conversación (opcional)

## Personalización

### Estilos

Los estilos del chatbot se pueden personalizar modificando los archivos CSS en los componentes Vue. La interfaz utiliza variables de color que se pueden ajustar para coincidir con la identidad visual de tu proyecto.

## Licencia

Este proyecto está licenciado bajo [TU LICENCIA] - ver el archivo LICENSE.md para más detalles.

## Autor

[Tu Nombre / Organización]

---

Desarrollado con ❤️ usando Laravel y Vue.js
