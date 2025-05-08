import { defineStore } from 'pinia';
import axios from 'axios';

export const useChatStore = defineStore('chat', {
    state: () => ({
        allMessages: [],         // Todos los mensajes (formato display)
        contextMessages: [],     // Mensajes en formato de contexto para la API
        displayedMessages: [],   // Mensajes mostrados actualmente
        messagesPerPage: 10,     // Número de mensajes a mostrar por página
        currentPage: 1,          // Página actual
        sessionId: null,
        isLoading: false,
        isLoadingMore: false,    // Indicador de carga de mensajes adicionales
        isOpen: false,
        hasMoreMessages: false,  // Indica si hay más mensajes para cargar
    }),
    
    actions: {
        initialize() {
            // Load messages from localStorage if available
            const storedMessages = localStorage.getItem('chat_messages');
            const storedContextMessages = localStorage.getItem('chat_context_messages');
            
            if (storedMessages) {
                try {
                    this.allMessages = JSON.parse(storedMessages);
                    
                    // Si tenemos mensajes de contexto almacenados, los cargamos
                    if (storedContextMessages) {
                        this.contextMessages = JSON.parse(storedContextMessages);
                    } else {
                        // Si no tenemos contexto pero sí mensajes, recreamos el contexto
                        this._recreateContextFromMessages();
                    }
                    
                    this._updateDisplayedMessages();
                } catch (e) {
                    console.error('Error parsing stored messages:', e);
                    localStorage.removeItem('chat_messages');
                    localStorage.removeItem('chat_context_messages');
                    this.allMessages = [];
                    this.contextMessages = [];
                    this.displayedMessages = [];
                }
            }
            
            // Get or create sessionId
            let storedSessionId = localStorage.getItem('chat_session_id');
            if (!storedSessionId) {
                storedSessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
                localStorage.setItem('chat_session_id', storedSessionId);
            }
            this.sessionId = storedSessionId;
            
            // Load chat visibility state
            const chatOpenState = localStorage.getItem('chat_is_open');
            this.isOpen = chatOpenState ? JSON.parse(chatOpenState) : false;
        },
        
        // Crea mensajes de contexto a partir de los mensajes existentes
        _recreateContextFromMessages() {
            this.contextMessages = this.allMessages.map(msg => ({
                role: msg.sender === 'user' ? 'user' : 'assistant',
                content: msg.content
            }));
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            localStorage.setItem('chat_is_open', JSON.stringify(this.isOpen));
        },
        
        // Actualiza los mensajes mostrados basados en la página actual
        _updateDisplayedMessages() {
            const totalMessages = this.allMessages.length;
            const startIndex = Math.max(0, totalMessages - (this.currentPage * this.messagesPerPage));
            const endIndex = totalMessages;
            
            // Actualizar los mensajes para mostrar
            this.displayedMessages = this.allMessages.slice(startIndex, endIndex);
            this.hasMoreMessages = startIndex > 0;
            
            // Forzar que Vue procese la actualización de inmediato para el correcto posicionamiento del scroll
            return Promise.resolve();
        },
        
        // Carga más mensajes antiguos
        loadMoreMessages() {
            if (!this.hasMoreMessages || this.isLoadingMore) return;
            
            this.isLoadingMore = true;
            
            // Guardar la longitud actual de mensajes antes de cargar más
            const currentLength = this.displayedMessages.length;
            
            // Obtener información del scroll del contenedor
            const scrollInfo = document.getElementById('chat-messages');
            const prevScrollHeight = scrollInfo ? scrollInfo.scrollHeight : 0;
            const prevScrollTop = scrollInfo ? scrollInfo.scrollTop : 0;
            
            // Obtener información del nodo visible si existe
            let visibleNodeInfo = null;
            if (scrollInfo && scrollInfo.dataset.visibleNodeInfo) {
                try {
                    visibleNodeInfo = JSON.parse(scrollInfo.dataset.visibleNodeInfo);
                    // Limpiar después de leer
                    delete scrollInfo.dataset.visibleNodeInfo;
                } catch (e) {
                    console.error('Error parsing visibleNodeInfo:', e);
                }
            }
            
            // Simulamos una pequeña latencia para dar feedback visual
            setTimeout(() => {
                this.currentPage++;
                
                // Actualizar mensajes mostrados
                this._updateDisplayedMessages()
                    .then(() => {
                        // Calcular cuántos mensajes nuevos se añadieron
                        const newMessagesCount = this.displayedMessages.length - currentLength;
                        
                        // Emitir evento custom para el componente
                        document.dispatchEvent(new CustomEvent('messagesLoaded', { 
                            detail: { 
                                newMessagesCount,
                                prevScrollHeight,
                                prevScrollTop,
                                visibleNodeInfo
                            } 
                        }));
                        
                        this.isLoadingMore = false;
                    });
            }, 300);
        },
        
        async sendMessage(message) {
            if (!message.trim()) return;
            
            // Add user message to chat
            const userMessage = {
                content: message,
                sender: 'user',
                timestamp: new Date().toISOString()
            };
            
            // Añadir a la lista de mensajes de visualización
            this.allMessages.push(userMessage);
            
            // Añadir a la lista de contexto para la API
            this.contextMessages.push({
                role: 'user',
                content: message
            });
            
            this._updateDisplayedMessages();
            
            // Save to localStorage
            this._saveToLocalStorage();
            
            // Set loading state
            this.isLoading = true;
            
            try {
                // Send message to our backend API with the context completo
                const response = await axios.post('/api/chatbot/message', {
                    message: message,
                    sessionId: this.sessionId,
                    messages: this.contextMessages // Enviar todo el historial de contexto
                });
                
                // Add bot response to chat
                if (response.data && response.data.response) {
                    // Mensaje para visualización
                    const botMessage = {
                        content: response.data.response,
                        sender: 'bot',
                        timestamp: new Date().toISOString()
                    };
                    
                    this.allMessages.push(botMessage);
                    
                    // Actualizar mensajes de contexto si el servidor los devuelve
                    if (response.data.messages && Array.isArray(response.data.messages)) {
                        this.contextMessages = response.data.messages;
                    } else {
                        // Si el servidor no devuelve el contexto, añadimos el mensaje del bot a nuestro contexto
                        this.contextMessages.push({
                            role: 'assistant',
                            content: response.data.response
                        });
                    }
                    
                    this._updateDisplayedMessages();
                } else if (response.data && response.data.error) {
                    const errorMessage = {
                        content: response.data.error,
                        sender: 'bot',
                        timestamp: new Date().toISOString()
                    };
                    
                    this.allMessages.push(errorMessage);
                    this._updateDisplayedMessages();
                } else {
                    // Handle case where response format is different
                    const fallbackMessage = {
                        content: 'Ho sento, no he pogut processar la teva petició.',
                        sender: 'bot',
                        timestamp: new Date().toISOString()
                    };
                    
                    this.allMessages.push(fallbackMessage);
                    this._updateDisplayedMessages();
                }
            } catch (error) {
                console.error('Chat API error:', error);
                
                // Extract error message if available
                let errorContent = 'Ho sento, hi ha hagut un error processant la teva petició. Si us plau, torna-ho a provar més tard.';
                
                if (error.response && error.response.data && error.response.data.error) {
                    errorContent = error.response.data.error;
                }
                
                const errorMessage = {
                    content: errorContent,
                    sender: 'bot',
                    timestamp: new Date().toISOString()
                };
                
                this.allMessages.push(errorMessage);
                this._updateDisplayedMessages();
            } finally {
                this.isLoading = false;
                // Save updated messages to localStorage
                this._saveToLocalStorage();
            }
        },
        
        clearChat() {
            this.allMessages = [];
            this.contextMessages = [];
            this.displayedMessages = [];
            this.currentPage = 1;
            this.hasMoreMessages = false;
            this._saveToLocalStorage();
        },
        
        _saveToLocalStorage() {
            try {
                localStorage.setItem('chat_messages', JSON.stringify(this.allMessages));
                localStorage.setItem('chat_context_messages', JSON.stringify(this.contextMessages));
            } catch (e) {
                console.error('Error saving chat messages to localStorage:', e);
            }
        }
    }
}); 