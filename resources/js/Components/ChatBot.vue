<template>
  <div class="chatbot-container" v-if="mounted">
    <!-- Chat bubble button -->
    <button 
      @click="toggleChat" 
      class="chat-bubble" 
      :class="{ 'has-new-messages': hasUnreadMessages }"
    >
      <span v-if="!isOpen" class="bubble-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
        </svg>
      </span>
      <span v-else class="bubble-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </span>
    </button>

    <!-- Chat panel -->
    <div class="chat-panel" v-show="isOpen">
      <div class="chat-header">
        <div class="header-logo">
          <svg width="24" height="24" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0H40V40H0V0Z" fill="#FFFFFF"/>
            <path d="M40 0H80V40H40V0Z" fill="#FFFFFF"/>
            <path d="M80 0H120V40H80V0Z" fill="#FFFFFF"/>
            <path d="M120 0H160V40H120V0Z" fill="#FFFFFF"/>
            <path d="M0 40H40V80H0V40Z" fill="#FFFFFF"/>
            <path d="M40 40H80V80H40V40Z" fill="#FFFFFF"/>
            <path d="M80 40H120V80H80V40Z" fill="#FFFFFF"/>
            <path d="M120 40H160V80H120V40Z" fill="#FFFFFF"/>
            <path d="M0 80H40V120H0V80Z" fill="#FFFFFF"/>
            <path d="M40 80H80V120H40V80Z" fill="#FFFFFF"/>
            <path d="M80 80H120V120H80V80Z" fill="#FFFFFF"/>
            <path d="M120 80H160V120H120V80Z" fill="#900000"/>
            <path d="M0 120H40V160H0V120Z" fill="#FFFFFF"/>
            <path d="M40 120H80V160H40V120Z" fill="#FFFFFF"/>
            <path d="M80 120H120V160H80V120Z" fill="#900000"/>
            <path d="M120 120H160V160H120V120Z" fill="#900000"/>
          </svg>
        </div>
        <h3>Assistent de Xat</h3>
        <button @click="clearChat" class="clear-button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
          </svg>
        </button>
      </div>
      
      <div id="chat-messages" ref="messagesContainer" class="chat-messages" @scroll="handleScroll">
        <!-- Botón para cargar más mensajes con diseño mejorado -->
        <Transition name="load-more-container">
          <div v-if="hasMoreMessages && showLoadMoreButton" class="load-more-container">
            <button 
              @click="loadMoreMessages" 
              class="load-more-button"
              :class="{ 'loading': isLoadingMore }"
              :disabled="isLoadingMore"
            >
              <span v-if="!isLoadingMore" class="load-more-content">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="load-more-icon">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" />
                </svg>
                <span class="load-more-text">Mostrar {{ messagesPerPage }} missatges més</span>
              </span>
              <span v-else class="loading-animation">
                <svg class="spinner" viewBox="0 0 50 50">
                  <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                </svg>
                <span>Carregant...</span>
              </span>
            </button>
          </div>
        </Transition>
        
        <div 
          v-for="(message, index) in displayedMessages" 
          :key="index" 
          class="message-wrapper"
          :class="[
            message.sender === 'user' ? 'user-message' : 'bot-message',
            index < displayedMessages.length - messagesPerPage ? 'history-message' : ''
          ]"
        >
          <div class="message">
            <div v-if="message.sender === 'user'" v-text="message.content"></div>
            <div v-else class="formatted-content" v-html="formatMessage(message.content)"></div>
          </div>
          <div class="message-time">
            {{ formatTime(message.timestamp) }}
          </div>
        </div>
        
        <!-- Elemento de referencia visible para scroll automático -->
        <div ref="lastMessageElement" class="end-message-marker"></div>
        
        <div v-if="isLoading" class="loading-indicator">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>
      
      <div class="chat-input">
        <input 
          ref="messageInput"
          type="text" 
          v-model="newMessage" 
          @keydown.enter.prevent="sendMessage"
          placeholder="Escriu el teu missatge aquí..."
          :disabled="isLoading"
        />
        <button 
          @click.prevent="sendMessage" 
          :disabled="isLoading || !newMessage.trim()"
          class="send-button"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick, onUpdated, onUnmounted } from 'vue';
import { useChatStore } from '../Stores/chatStore';
import DOMPurify from 'dompurify';
import { marked } from 'marked';

// Configurar Marked para opciones seguras
marked.setOptions({
  headerIds: false,
  mangle: false
});

const chatStore = useChatStore();
const messagesContainer = ref(null);
const lastMessageElement = ref(null);
const messageInput = ref(null);
const newMessage = ref('');
const mounted = ref(false);
const hasUnreadMessages = ref(false);
const shouldRetainFocus = ref(false);
const scrollThreshold = 30; // Umbral para detectar scroll hacia la parte superior
const showLoadMoreButton = ref(false); // Controla la visibilidad del botón de cargar más

const displayedMessages = computed(() => chatStore.displayedMessages);
const isLoading = computed(() => chatStore.isLoading);
const isLoadingMore = computed(() => chatStore.isLoadingMore);
const isOpen = computed(() => chatStore.isOpen);
const hasMoreMessages = computed(() => chatStore.hasMoreMessages);
const messagesPerPage = computed(() => chatStore.messagesPerPage);

onMounted(() => {
  // Inicializar el store antes de cualquier manipulación DOM
  chatStore.initialize();
  
  // Marcar como montado inmediatamente
  mounted.value = true;
  
  // Usar requestAnimationFrame para asegurar que el navegador procese primero
  // todos los elementos antes de intentar hacer scroll
  requestAnimationFrame(() => {
    if (isOpen.value && displayedMessages.value.length > 0) {
      // Forzar scroll instantáneo sin comportamiento suave
      if (lastMessageElement.value) {
        lastMessageElement.value.scrollIntoView({ block: 'end', inline: 'nearest' });
      } else if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
      }
      
      // Segundo intento para garantizar el scroll
      setTimeout(() => {
        scrollToBottomWithoutAnimation();
      }, 50);
    }
  });

  // Agregar event listener para el evento de carga de mensajes antiguos
  document.addEventListener('messagesLoaded', handleMessagesLoaded);
  
  // Limpiar event listener cuando el componente se desmonta
  onUnmounted(() => {
    document.removeEventListener('messagesLoaded', handleMessagesLoaded);
  });
});

// Nueva función para scroll sin animación
function scrollToBottomWithoutAnimation() {
  if (messagesContainer.value) {
    // Deshabilitar temporalmente el comportamiento suave del scroll
    messagesContainer.value.style.scrollBehavior = 'auto';
    
    // Aplicar scroll
    if (lastMessageElement.value) {
      lastMessageElement.value.scrollIntoView({ block: 'end', inline: 'nearest' });
    } else {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
    
    // Restaurar comportamiento después de un breve retraso
    setTimeout(() => {
      messagesContainer.value.style.scrollBehavior = 'smooth';
    }, 100);
  }
}

// Función para manejar el scroll y controlar la visibilidad del botón de cargar más
function handleScroll(e) {
  // Mostrar el botón solo cuando el usuario está cerca del inicio del chat
  if (hasMoreMessages.value) {
    // Solo mostrar el botón cuando el scroll está muy cerca del tope superior
    showLoadMoreButton.value = e.target.scrollTop <= scrollThreshold;
  } else {
    showLoadMoreButton.value = false;
  }
}

function loadMoreMessages() {
  if (isLoadingMore.value) return;
  
  // Capturar la posición actual antes de cargar
  const scrollInfo = saveScrollPosition();
  
  // Guardar referencia al nodo visible de referencia
  let visibleNodeRef = null;
  if (scrollInfo && scrollInfo.visibleNode) {
    // Guardar algún atributo del nodo que nos permita identificarlo después
    const index = Array.from(messagesContainer.value.querySelectorAll('.message-wrapper'))
      .indexOf(scrollInfo.visibleNode);
    
    if (index !== -1) {
      visibleNodeRef = {
        index,
        contentStart: scrollInfo.visibleNode.textContent.substring(0, 20) // texto para identificar
      };
    }
  }
  
  // Pasar la información al evento como dato personalizado
  if (scrollInfo) {
    messagesContainer.value.dataset.prevScrollHeight = scrollInfo.scrollHeight;
    messagesContainer.value.dataset.prevScrollTop = scrollInfo.scrollTop;
    if (visibleNodeRef) {
      messagesContainer.value.dataset.visibleNodeInfo = JSON.stringify(visibleNodeRef);
    }
  }
  
  // Cargar mensajes antiguos
  chatStore.loadMoreMessages();
}

// Guarda la posición actual del scroll y elementos de referencia para restaurarla después
function saveScrollPosition() {
  if (!messagesContainer.value) return null;
  
  // Guardar la altura total del scroll
  const scrollHeight = messagesContainer.value.scrollHeight;
  
  // Guardar la posición actual del scroll
  const scrollTop = messagesContainer.value.scrollTop;
  
  // Si hay al menos un mensaje visible, guardar su referencia
  let visibleNode = null;
  if (displayedMessages.value.length > 0) {
    // Encontrar un nodo que esté completamente visible
    const messageNodes = messagesContainer.value.querySelectorAll('.message-wrapper');
    for (const node of messageNodes) {
      const rect = node.getBoundingClientRect();
      const containerRect = messagesContainer.value.getBoundingClientRect();
      
      // Si el nodo está visible en el viewport del contenedor
      if (rect.top >= containerRect.top && rect.bottom <= containerRect.bottom) {
        visibleNode = node;
        break;
      }
    }
  }
  
  return { scrollHeight, scrollTop, visibleNode };
}

// Observar cuando cambia el estado de carga para mantener el foco
watch(() => chatStore.isLoading, (newIsLoading, oldIsLoading) => {
  // Si terminó de cargar (pasó de cargando a no cargando)
  if (oldIsLoading && !newIsLoading && shouldRetainFocus.value) {
    // Restaurar foco al input después de recibir respuesta
    nextTick(() => {
      if (messageInput.value) {
        messageInput.value.focus();
      }
    });
    shouldRetainFocus.value = false;
  }
});

// Observando cuando la visibilidad del panel cambia
watch(isOpen, (newValue) => {
  if (newValue === true) {
    hasUnreadMessages.value = false;
    
    // Scroll instantáneo cuando se abre el chat
    nextTick(() => {
      scrollToBottomWithoutAnimation();
    });
  }
});

// Observando cuando cambian los mensajes
watch(() => displayedMessages.value.length, () => {
  // Scroll instantáneo cuando hay nuevos mensajes
  nextTick(() => {
    scrollToBottomWithoutAnimation();
  });
  
  // Si el chat está cerrado y llegó un mensaje del bot, marcar como no leído
  if (!isOpen.value && displayedMessages.value.length > 0) {
    const lastMessage = displayedMessages.value[displayedMessages.value.length - 1];
    if (lastMessage.sender === 'bot') {
      hasUnreadMessages.value = true;
    }
  }
});

// Scroll forzado después de cada actualización del componente
onUpdated(() => {
  if (isOpen.value && displayedMessages.value.length > 0) {
    scrollToBottomWithoutAnimation();
  }
});

// MutationObserver para detectar cambios en el DOM del contenedor de mensajes
onMounted(() => {
  if (typeof MutationObserver !== 'undefined' && messagesContainer.value) {
    const observer = new MutationObserver(() => {
      scrollToBottomWithoutAnimation();
    });
    
    observer.observe(messagesContainer.value, {
      childList: true,
      subtree: true
    });
  }
});

// Función para formatear el contenido del mensaje con Markdown
function formatMessage(content) {
  if (!content) return '';
  
  try {
    // Pre-procesar el texto para mejorar el formateo de listas con días
    // Este paso es crucial para textos que vienen sin formato Markdown apropiado
    content = preprocessEventLists(content);
    
    // Aplicar formato Markdown con configuración para preservar espacios
    marked.setOptions({
      headerIds: false,
      mangle: false,
      breaks: true,     // Convertir saltos de línea en <br>
      gfm: true,        // GitHub Flavored Markdown para mejor compatibilidad
      smartLists: true, // Listas más inteligentes
      xhtml: true       // Cerrar tags (xhtml compliant)
    });
    
    // Aplicar formato Markdown
    let html = marked(content);
    
    // Sanitizar HTML para evitar XSS
    if (typeof DOMPurify !== 'undefined') {
      html = DOMPurify.sanitize(html, {
        ADD_ATTR: ['target'], // Permitir atributo target para enlaces
        ALLOW_DATA_ATTR: true // Permitir atributos data-*
      });
    }
    
    // Post-procesamiento para mejorar la estructura visual del HTML generado
    html = postprocessHTML(html);
    
    return html;
  } catch (e) {
    console.error('Error al formatear mensaje:', e);
    // En caso de error, aplicar formato básico para mantener los saltos de línea
    return content.replace(/\n/g, '<br>');
  }
}

// Función para pre-procesar textos con formato de eventos
function preprocessEventLists(text) {
  // Si no contiene ningún formato de eventos, devolver tal cual
  if (!text.includes('Lugar:') && !text.includes('Descripción:')) {
    return text;
  }
  
  // Asegurar que las líneas de "Día:" tienen formato de lista Markdown
  const lines = text.split('\n');
  let processed = [];
  
  for (let i = 0; i < lines.length; i++) {
    let line = lines[i].trim();
    
    // Detectar líneas de día de la semana (e.g., "Lunes: Formación...")
    if (/^(?:Lunes|Martes|Miércoles|Jueves|Viernes|Sábado|Domingo):/i.test(line) && 
        !line.startsWith('-') && !line.startsWith('*')) {
      // Si la línea no tiene formato de lista, añadirlo
      processed.push(`- **${line}**`);
    } 
    // Detectar líneas de detalles (Lugar, Descripción)
    else if (line.startsWith('Lugar:') || line.startsWith('Descripción:')) {
      // Formatear como sublista con guiones
      processed.push(`  - **${line.split(':')[0]}:** ${line.split(':').slice(1).join(':').trim()}`);
    } 
    // Cualquier otra línea, mantenerla igual
    else {
      processed.push(line);
    }
  }
  
  return processed.join('\n');
}

// Función para post-procesar el HTML y mejorar la estructura visual
function postprocessHTML(html) {
  // Reemplazar títulos de secciones con versiones más estilizadas
  html = html.replace(/<h3>(.*?)<\/h3>/g, '<div class="section-title">$1</div>');
  
  // Mejorar el formato de las listas
  html = html.replace(/<ul>/g, '<ul class="formatted-list">');
  html = html.replace(/<ol>/g, '<ol class="formatted-list">');
  
  // Mejorar la apariencia de los elementos de lista
  html = html.replace(/<li>/g, '<li class="formatted-list-item">');
  
  // Mejorar la apariencia de los elementos de código
  html = html.replace(/<code>/g, '<code class="inline-code">');
  
  // Agregar clases a los elementos de eventos
  html = html.replace(/<strong>(Lugar|Descripción):<\/strong>/g, 
    '<strong class="event-detail-label">$1:</strong>');
  
  // Agregar clases a días de la semana
  html = html.replace(/<strong>(Lunes|Martes|Miércoles|Jueves|Viernes|Sábado|Domingo):<\/strong>/g, 
    '<strong class="event-day-label">$1:</strong>');
  
  // Envolver elementos de evento en contenedores para mejor presentación
  html = html.replace(
    /<li class="formatted-list-item"><strong class="event-day-label">(.*?)<\/strong>(.*?)<ul class="formatted-list">/g, 
    '<li class="formatted-list-item event-item"><div class="event-header"><strong class="event-day-label">$1</strong>$2</div><ul class="formatted-list event-details">'
  );
  
  return html;
}

function sendMessage() {
  if (newMessage.value.trim() === '' || isLoading.value) return;
  
  // Registrar si el input tenía el foco antes de enviar
  const hadFocus = document.activeElement === messageInput.value;
  
  // Si tenía el foco, indicar que debe retenerlo cuando llegue la respuesta
  if (hadFocus) {
    shouldRetainFocus.value = true;
  }
  
  chatStore.sendMessage(newMessage.value);
  newMessage.value = '';
  
  // Hacer scroll hasta abajo inmediatamente después de enviar
  scrollToBottomWithoutAnimation();
  
  // Mantener el foco inmediatamente (para la experiencia fluida de escritura)
  if (hadFocus) {
    nextTick(() => {
      if (messageInput.value) {
        messageInput.value.focus();
      }
    });
  }
}

function toggleChat() {
  chatStore.toggleChat();
  
  // Si se está abriendo el chat, enfocar el input después de que sea visible
  if (!isOpen.value) {
    nextTick(() => {
      if (messageInput.value) {
        // Pequeño retraso para asegurar que la transición de apertura ya ocurrió
        setTimeout(() => {
          messageInput.value.focus();
        }, 200);
      }
    });
  }
}

function clearChat() {
  chatStore.clearChat();
}

function formatTime(timestamp) {
  if (!timestamp) return '';
  
  const date = new Date(timestamp);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Función para manejar la carga de mensajes adicionales
function handleMessagesLoaded(event) {
  if (!messagesContainer.value) return;
  
  // Obtener información del scroll después de cargar nuevos mensajes
  const { scrollHeight: newScrollHeight } = messagesContainer.value;
  
  // Calcular la altura de los nuevos mensajes añadidos
  const heightDifference = newScrollHeight - event.detail?.prevScrollHeight;
  
  // Restaurar la posición del scroll para mantener la misma vista relativa
  nextTick(() => {
    // Desactivar animación para este ajuste
    messagesContainer.value.style.scrollBehavior = 'auto';
    
    // Intentar localizar el nodo visible de referencia si existe
    let referenceNode = null;
    if (event.detail?.visibleNodeInfo) {
      const { index, contentStart } = event.detail.visibleNodeInfo;
      const messageNodes = messagesContainer.value.querySelectorAll('.message-wrapper');
      
      // Buscar el nodo con un índice similar + offset por los nuevos mensajes
      const targetIndex = index + event.detail.newMessagesCount;
      if (targetIndex < messageNodes.length) {
        referenceNode = messageNodes[targetIndex];
      }
      
      // Verificación adicional por contenido 
      if (!referenceNode && contentStart) {
        for (const node of messageNodes) {
          if (node.textContent.includes(contentStart)) {
            referenceNode = node;
            break;
          }
        }
      }
    }
    
    if (referenceNode) {
      // Si tenemos un nodo de referencia, scrollear a él
      referenceNode.scrollIntoView({ block: 'nearest', behavior: 'auto' });
    } else {
      // De lo contrario, ajustar manualmente la posición
      const newScrollTop = event.detail?.prevScrollTop + heightDifference;
      messagesContainer.value.scrollTop = newScrollTop;
    }
    
    // Restaurar comportamiento de scroll después
    setTimeout(() => {
      messagesContainer.value.style.scrollBehavior = 'smooth';
      
      // Restablecer estado de carga
      showLoadMoreButton.value = messagesContainer.value.scrollTop <= scrollThreshold;
    }, 100);
  });
}
</script>

<style scoped>
/* Estilos de Gencat */
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap');

.chatbot-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
  font-family: 'Open Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.chat-bubble {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #900000; /* Color principal actualizado */
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border: none;
  transition: background-color 0.3s, transform 0.2s;
}

.chat-bubble:hover {
  background-color: #700000; /* Versión más oscura del color principal */
  transform: scale(1.05);
}

.chat-bubble.has-new-messages::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: #FFBF00; /* Amarillo para notificaciones */
  border: 2px solid white;
}

.bubble-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-panel {
  position: absolute;
  bottom: 70px;
  right: 0;
  width: 350px;
  height: 500px;
  background-color: white;
  border-radius: 8px; /* Gencat usa bordes menos redondeados */
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #E5E7EB;
}

.chat-header {
  padding: 12px 16px;
  background-color: #900000; /* Color principal actualizado */
  color: white;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #700000; /* Versión más oscura del color principal */
}

.header-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.chat-header h3 {
  margin: 0;
  font-size: 16px;
  flex-grow: 1;
}

.clear-button {
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.clear-button:hover {
  opacity: 1;
}

/* Estilos para cargar más mensajes */
.load-more-container {
  display: flex;
  justify-content: center;
  padding: 10px 0;
  margin-bottom: 12px;
  position: sticky;
  top: 0;
  z-index: 10;
  background: linear-gradient(180deg, rgba(249, 250, 251, 0.98) 70%, rgba(249, 250, 251, 0.85) 100%);
  backdrop-filter: blur(8px);
  border-radius: 0 0 12px 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: opacity 0.3s, transform 0.3s;
  opacity: 1;
  transform: translateY(0);
}

.load-more-button {
  background: linear-gradient(135deg, #900000 0%, #b10000 100%);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 10px 18px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 200px;
  min-height: 40px;
  box-shadow: 0 4px 12px rgba(144, 0, 0, 0.25);
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.load-more-icon {
  width: 16px;
  height: 16px;
  margin-right: 6px;
  color: white;
}

.load-more-button:hover:not(.loading) {
  background: linear-gradient(135deg, #ad0000 0%, #c60000 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(144, 0, 0, 0.3);
}

.load-more-button:active:not(.loading) {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(144, 0, 0, 0.25);
}

.load-more-button.loading {
  background: linear-gradient(135deg, #9b2626 0%, #a93333 100%);
  opacity: 0.9;
  cursor: default;
}

.loading-animation {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.loading-animation .spinner {
  width: 20px;
  height: 20px;
  animation: spin 1.2s linear infinite;
}

.loading-animation .spinner .path {
  stroke: white;
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
  stroke-dasharray: 90, 150;
  stroke-dashoffset: 0;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 150;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -124;
  }
}

.load-more-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.load-more-text {
  font-weight: 600;
  letter-spacing: 0.3px;
  font-size: 11px;
}

/* Estilos mejorados para el contenedor de mensajes y scroll */
.chat-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background-color: #F9FAFB; /* Fondo gris claro */
  
  /* Desactivar el comportamiento suave inicialmente para evitar scroll automático animado */
  scroll-behavior: auto !important;
  
  /* Margen para que los mensajes no queden pegados a la scrollbar */
  padding-right: 20px;
  
  /* Estilizar la barra de desplazamiento para WebKit (Chrome, Safari, etc.) */
  &::-webkit-scrollbar {
    width: 8px;
    background-color: transparent;
  }
  
  &::-webkit-scrollbar-track {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    margin: 10px 0;
  }
  
  &::-webkit-scrollbar-thumb {
    background-color: rgba(144, 0, 0, 0.3);
    border-radius: 10px;
    transition: background-color 0.3s;
  }
  
  &::-webkit-scrollbar-thumb:hover {
    background-color: rgba(144, 0, 0, 0.5);
  }
  
  /* Estilizar la barra de desplazamiento para Firefox */
  scrollbar-width: thin;
  scrollbar-color: rgba(144, 0, 0, 0.3) rgba(0, 0, 0, 0.05);
}

/* Animación suave para nuevos mensajes */
.message-wrapper {
  display: flex;
  flex-direction: column;
  max-width: 80%;
  animation: none; /* Desactivar animación por defecto */
}

/* Solo aplicar animaciones para los mensajes nuevos al final,
   no para los mensajes que se cargan desde el historial */
.message-wrapper:nth-last-child(-n+3):not(.history-message) {
  opacity: 0;
  transform: translateY(10px);
  animation: messageAppear 0.3s forwards;
}

/* Clase especial para mensajes históricos cargados */
.message-wrapper.history-message {
  opacity: 1;
  transform: none;
}

@keyframes messageAppear {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.end-message-marker {
  min-height: 1px;
  width: 100%;
  padding-bottom: 16px; /* Espacio adicional abajo para evitar que los mensajes queden cortados */
  scroll-margin-bottom: 20px; /* Asegura espacio adicional cuando se hace scroll a este elemento */
}

.user-message {
  align-self: flex-end;
}

.bot-message {
  align-self: flex-start;
  max-width: 85%; /* Aumentar el ancho máximo para las respuestas del bot */
}

.message {
  padding: 12px 16px;
  border-radius: 8px; /* Gencat usa bordes menos redondeados */
  font-size: 14px;
  line-height: 1.5;
  word-wrap: break-word;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
}

.message:hover {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.user-message .message {
  background-color: #900000; /* Color principal actualizado */
  color: white;
  border-bottom-right-radius: 2px;
}

.bot-message .message {
  background-color: white;
  color: #333333; /* Negro actualizado */
  border-bottom-left-radius: 2px;
  border: 1px solid #E5E7EB;
  width: 100%; /* Asegurar que el mensaje ocupe todo el ancho disponible */
}

.message-time {
  font-size: 11px;
  margin-top: 4px;
  color: #6B7280;
  align-self: flex-end;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.message-wrapper:hover .message-time {
  opacity: 1;
}

.bot-message .message-time {
  align-self: flex-start;
}

/* Estilos para contenido formateado */
.formatted-content {
  width: 100%;
}

.formatted-content p {
  margin: 0.5em 0;
}

.formatted-content p:first-child {
  margin-top: 0;
}

.formatted-content p:last-child {
  margin-bottom: 0;
}

.section-title {
  font-weight: 700;
  font-size: 16px;
  margin: 10px 0 5px 0;
  color: #900000;
  padding-bottom: 4px;
  border-bottom: 1px solid rgba(144, 0, 0, 0.2);
}

.formatted-list {
  margin: 6px 0;
  padding-left: 20px;
}

.formatted-list-item {
  margin: 6px 0;
  position: relative;
  line-height: 1.4;
}

/* Formatear listas anidadas */
.formatted-list .formatted-list {
  margin: 8px 0 8px 5px;
  padding-left: 15px;
  border-left: 1px solid rgba(144, 0, 0, 0.15);
}

/* Mejora del formato para elementos de definición (ubicación, descripción) */
.formatted-list-item p {
  margin: 2px 0 !important;
}

/* Aumentar el espacio entre items principales de la lista */
.formatted-list > .formatted-list-item {
  margin-bottom: 12px;
}

/* Último elemento de la lista sin margen */
.formatted-list > .formatted-list-item:last-child {
  margin-bottom: 4px;
}

.formatted-list li::marker {
  color: #900000;
  font-weight: bold;
}

.inline-code {
  background-color: rgba(0, 0, 0, 0.05);
  padding: 2px 4px;
  border-radius: 3px;
  font-family: monospace;
  font-size: 13px;
}

pre {
  background-color: #f5f5f5;
  border-radius: 4px;
  padding: 8px;
  overflow-x: auto;
  margin: 8px 0;
}

pre code {
  font-family: monospace;
  font-size: 13px;
  line-height: 1.4;
}

.highlight {
  color: #900000;
  font-weight: bold;
}

blockquote {
  border-left: 3px solid #900000;
  margin: 8px 0;
  padding-left: 10px;
  color: #555;
  font-style: italic;
}

.bot-message .message a {
  color: #900000;
  text-decoration: none;
  font-weight: 600;
  transition: text-decoration 0.2s;
}

.bot-message .message a:hover {
  text-decoration: underline;
}

/* Indicador de carga elegante */
.loading-indicator {
  display: flex;
  gap: 4px;
  padding: 10px 14px;
  align-self: flex-start;
  background-color: rgba(255, 255, 255, 0.9);
  border-radius: 8px;
  border-bottom-left-radius: 2px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.dot {
  width: 8px;
  height: 8px;
  background-color: #900000; /* Color principal actualizado */
  border-radius: 50%;
  animation: bounce 1.4s infinite ease-in-out;
  opacity: 0.8;
}

.dot:nth-child(1) {
  animation-delay: 0s;
}

.dot:nth-child(2) {
  animation-delay: 0.2s;
}

.dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes bounce {
  0%, 80%, 100% {
    transform: translateY(0);
    opacity: 0.5;
  }
  40% {
    transform: translateY(-6px);
    opacity: 1;
  }
}

.chat-input {
  padding: 12px 16px;
  display: flex;
  gap: 8px;
  border-top: 1px solid #E5E7EB;
  background-color: white;
}

.chat-input input {
  flex: 1;
  padding: 10px 14px;
  border: 1px solid #D1D5DB;
  border-radius: 4px; /* Gencat usa bordes menos redondeados */
  outline: none;
  font-size: 14px;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: 'Open Sans', sans-serif;
  color: #333333; /* Negro actualizado */
}

.chat-input input:focus {
  border-color: #900000; /* Color principal actualizado */
  box-shadow: 0 0 0 2px rgba(144, 0, 0, 0.1);
}

.send-button {
  width: 40px;
  height: 40px;
  border-radius: 4px; /* Gencat usa bordes menos redondeados */
  background-color: #900000; /* Color principal actualizado */
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s, transform 0.1s;
}

.send-button:hover:not(:disabled) {
  background-color: #700000; /* Versión más oscura del color principal */
}

.send-button:active:not(:disabled) {
  transform: scale(0.95);
}

.send-button:disabled {
  background-color: #9CA3AF;
  cursor: not-allowed;
}

/* Cuando el botón está oculto (v-if), quedará preparado para la transición al aparecer */
.load-more-container-enter-active, 
.load-more-container-leave-active {
  transition: opacity 0.4s, transform 0.4s;
}

.load-more-container-enter-from,
.load-more-container-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Formato específico para eventos */
.formatted-content strong {
  display: inline-block;
  color: #900000;
}

.formatted-content strong + br,
.formatted-content br + br {
  display: none; /* Eliminar espacios excesivos */
}

/* Mejorar la visualización de los detalles de eventos */
.formatted-list .formatted-list {
  position: relative;
  margin-top: 3px;
  margin-bottom: 8px;
}

/* Sangría uniforme para elementos anidados */
.formatted-list-item ul.formatted-list {
  margin-left: 0;
  border-left: 2px solid rgba(144, 0, 0, 0.15);
  padding-top: 4px;
  padding-bottom: 4px;
}

/* Estilo para detalles de eventos (ubicación, descripción) */
.formatted-list .formatted-list .formatted-list-item {
  margin: 4px 0;
  padding-left: 4px;
}

/* Mensaje maximizado para mostrar todo el contenido */
.bot-message .message {
  padding: 14px 18px;
  max-width: 100%;
}

/* Añadir espacio después de los párrafos pero antes de listas */
.formatted-content p + .formatted-list {
  margin-top: 8px;
}

/* Espaciado para el día de la semana en negrita */
.formatted-list-item > strong {
  margin-right: 5px;
}

/* Estilos personalizados para formato de eventos */
.event-item {
  margin-bottom: 16px !important;
  border-bottom: 1px solid rgba(144, 0, 0, 0.1);
  padding-bottom: 12px;
}

.event-item:last-child {
  border-bottom: none;
  margin-bottom: 0 !important;
}

.event-header {
  margin-bottom: 6px;
  font-weight: 500;
}

.event-day-label {
  color: #900000;
  font-weight: 700;
  margin-right: 4px;
  font-size: 15px;
}

.event-details {
  background-color: rgba(144, 0, 0, 0.03);
  border-radius: 4px;
  padding: 8px 12px !important;
  margin-left: 5px !important;
  border-left: 2px solid rgba(144, 0, 0, 0.2);
}

.event-detail-label {
  color: #333333;
  font-weight: 600;
  margin-right: 4px;
  min-width: 90px;
  display: inline-block;
}

/* Mejorar la separación entre los elementos de detalles */
.event-details .formatted-list-item {
  margin: 4px 0 !important;
  display: flex;
  align-items: flex-start;
}

/* Asegurar que el contenido sea legible */
.bot-message .message {
  font-size: 14px;
  line-height: 1.6;
  color: #333333;
}

/* Estilo adicional para garantizar que las listas se vean bien */
.formatted-list {
  list-style-type: none !important;
  padding-left: 0 !important;
}

.formatted-list-item {
  padding-left: 0 !important;
}

/* Restaurar viñetas para sublistas de detalles */
.event-details.formatted-list {
  list-style-type: none !important;
}

/* Evitar que el texto se corte */
.bot-message .message p {
  margin: 6px 0;
  white-space: normal;
}
</style> 