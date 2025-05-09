<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import ChatBot from '@/Components/ChatBot.vue';
import { ref, onMounted } from 'vue';

// Animation variables
const animationActive = ref(false);

// Manage animation on mount
onMounted(() => {
    setTimeout(() => {
        animationActive.value = true;
    }, 300);
});
</script>

<template>
    <div class="futuristic-container">
        <!-- Background elements -->
        <div class="circuit-bg"></div>
        <div class="grid-lines"></div>
        
        <!-- Institutional shield in top left -->
        <div class="institutional-shield">
            <Link href="/">
                <svg viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg">
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
                    <path d="M120 80H160V120H120V80Z" fill="#0066CC"/>
                    <path d="M0 120H40V160H0V120Z" fill="#FFFFFF"/>
                    <path d="M40 120H80V160H40V120Z" fill="#FFFFFF"/>
                    <path d="M80 120H120V160H80V120Z" fill="#0066CC"/>
                    <path d="M120 120H160V160H120V120Z" fill="#0066CC"/>
                </svg>
            </Link>
        </div>
        
        <!-- Main content container -->
        <div class="auth-content" :class="{ 'active': animationActive }">
            <!-- Logo with modern styling -->
            <div class="logo-container">
                <Link href="/">
                    <div class="auth-logo">
                        <div class="robot-head">
                            <div class="robot-eyes">
                                <div class="robot-eye left"></div>
                                <div class="robot-eye right"></div>
                            </div>
                            <div class="robot-antenna"></div>
                            <div class="robot-mouth"></div>
                        </div>
                    </div>
                    <h1 class="logo-text">AssistActesBot</h1>
                </Link>
            </div>

            <!-- Auth form container with holographic styling -->
            <div class="auth-panel">
                <div class="panel-highlight"></div>
                <div class="panel-content">
                    <slot />
                </div>
            </div>
        </div>
        
        <!-- Add ChatBot component -->
        <ChatBot />
        
        <!-- Floating decorative elements -->
        <div class="floating-element left"></div>
        <div class="floating-element right"></div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700&family=Roboto:wght@300;400;500&display=swap');

/* Base container styling */
.futuristic-container {
    min-height: 100vh;
    width: 100%;
    background-color: #0a0f25;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-family: 'Exo 2', sans-serif;
    color: white;
}

/* Background elements */
.circuit-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 10% 20%, rgba(0, 102, 204, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(0, 102, 204, 0.1) 0%, transparent 50%),
        url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,10 L90,10 M10,20 L30,20 M70,20 L90,20 M10,30 L20,30 M50,30 L90,30 M10,40 L40,40 M60,40 L90,40 M10,50 L50,50 M70,50 L90,50 M10,60 L30,60 M50,60 L90,60 M10,70 L40,70 M70,70 L90,70 M10,80 L20,80 M60,80 L90,80 M10,90 L90,90' stroke='rgba(0,102,204,0.15)' stroke-width='0.5'/%3E%3C/svg%3E");
    opacity: 0.7;
    z-index: 0;
}

.grid-lines {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(0, 102, 204, 0.1) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(0, 102, 204, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    z-index: 0;
}

/* Institutional shield */
.institutional-shield {
    position: absolute;
    top: 20px;
    left: 20px;
    width: 40px;
    height: 40px;
    z-index: 2;
    opacity: 0.8;
    filter: drop-shadow(0 0 10px rgba(0, 102, 204, 0.8));
    animation: pulse 5s infinite alternate;
}

@keyframes pulse {
    0% { filter: drop-shadow(0 0 10px rgba(0, 102, 204, 0.5)); }
    100% { filter: drop-shadow(0 0 15px rgba(0, 102, 204, 0.9)); }
}

/* Main content area */
.auth-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    max-width: 500px;
    width: 100%;
    padding: 2rem;
    margin: 0 auto;
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.auth-content.active {
    transform: translateY(0);
    opacity: 1;
}

/* Logo styling */
.logo-container {
    margin-bottom: 2rem;
    text-align: center;
}

.logo-container a {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
}

.auth-logo {
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 0.75rem;
}

.logo-text {
    font-size: 1.8rem;
    font-weight: 700;
    margin-top: 0.5rem;
    background: linear-gradient(to right, #FFFFFF 0%, #0066CC 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    position: relative;
    text-shadow: 0 2px 10px rgba(0, 102, 204, 0.4);
}

/* Robot head in logo */
.robot-head {
    width: 60px;
    height: 50px;
    background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
    border-radius: 15px 15px 10px 10px;
    position: relative;
    box-shadow: 0 0 20px rgba(0, 102, 204, 0.7);
    overflow: hidden;
}

.robot-head::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.1);
    top: 0;
    left: 0;
    border-radius: 15px 15px 0 0;
}

.robot-eyes {
    display: flex;
    justify-content: space-around;
    padding-top: 12px;
}

.robot-eye {
    width: 12px;
    height: 12px;
    background: white;
    border-radius: 50%;
    position: relative;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
}

.robot-eye::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 6px;
    background: #0a0f25;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: blinkEyes 5s infinite;
}

@keyframes blinkEyes {
    0%, 95%, 98% { transform: translate(-50%, -50%) scale(1); }
    96%, 97% { transform: translate(-50%, -50%) scale(0.1, 0.01); }
}

.robot-mouth {
    width: 20px;
    height: 4px;
    background: rgba(255, 255, 255, 0.7);
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.robot-antenna {
    width: 4px;
    height: 12px;
    background: #A0A0A0;
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
}

.robot-antenna::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 6px;
    background: #FF3366;
    border-radius: 50%;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 0 5px #FF3366;
    animation: antennaGlow 1.5s infinite alternate;
}

@keyframes antennaGlow {
    from { box-shadow: 0 0 5px #FF3366; }
    to { box-shadow: 0 0 10px #FF3366; }
}

/* Auth panel styling */
.auth-panel {
    width: 100%;
    max-width: 450px;
    position: relative;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    box-shadow: 0 8px 32px 0 rgba(0, 102, 204, 0.2);
    border: 1px solid rgba(0, 102, 204, 0.3);
    overflow: hidden;
    transition: all 0.3s ease;
}

.auth-panel:hover {
    box-shadow: 0 8px 32px 0 rgba(0, 102, 204, 0.3);
    border: 1px solid rgba(0, 102, 204, 0.4);
}

.panel-highlight {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, transparent, #0066CC, transparent);
    opacity: 0.8;
    animation: shimmerHighlight 3s infinite;
}

@keyframes shimmerHighlight {
    0% { opacity: 0.5; background-position: -100% 0; }
    50% { opacity: 1; }
    100% { opacity: 0.5; background-position: 200% 0; }
}

.panel-content {
    padding: 2rem;
    color: #E0E6FF;
}

/* Override form input elements to match theme */
:deep(.w-full) {
    background-color: rgba(255, 255, 255, 0.07) !important;
    border: 1px solid rgba(0, 102, 204, 0.3) !important;
    color: white !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
}

:deep(.w-full:focus) {
    border-color: #0066CC !important;
    box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2) !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
}

:deep(label) {
    color: #A0D2FF !important;
    font-weight: 500 !important;
}

:deep(button[type="submit"], .bg-gray-800) {
    background: linear-gradient(135deg, #0066CC 0%, #004080 100%) !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3) !important;
    transition: all 0.3s ease !important;
    color: white !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

:deep(button[type="submit"]:hover:not(:disabled), .bg-gray-800:hover) {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4) !important;
    background: linear-gradient(135deg, #0077E6 0%, #0055AA 100%) !important;
}

:deep(button[type="submit"]:active:not(:disabled), .bg-gray-800:active) {
    transform: translateY(0) !important;
    box-shadow: 0 2px 10px rgba(0, 102, 204, 0.3) !important;
}

:deep(a) {
    color: #A0D2FF !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    position: relative !important;
}

:deep(a::after) {
    content: '' !important;
    position: absolute !important;
    width: 100% !important;
    height: 1px !important;
    bottom: -2px !important;
    left: 0 !important;
    background-color: #0066CC !important;
    transform: scaleX(0) !important;
    transform-origin: bottom right !important;
    transition: transform 0.3s ease !important;
}

:deep(a:hover) {
    color: #FFFFFF !important;
}

:deep(a:hover::after) {
    transform: scaleX(1) !important;
    transform-origin: bottom left !important;
}

:deep(.text-gray-600), :deep(.text-gray-500) {
    color: #8AABFF !important;
}

:deep(.text-green-600) {
    color: #00FF9B !important;
}

:deep(.text-red-600) {
    color: #FF3366 !important;
}

/* Floating decorative elements */
.floating-element {
    position: absolute;
    background: rgba(0, 102, 204, 0.2);
    border: 1px solid rgba(0, 102, 204, 0.4);
    border-radius: 50%;
    filter: blur(2px);
    z-index: 0;
}

.floating-element.left {
    width: 200px;
    height: 200px;
    left: -80px;
    top: 25%;
    animation: float 20s infinite ease-in-out;
}

.floating-element.right {
    width: 250px;
    height: 250px;
    right: -100px;
    bottom: 15%;
    animation: float 15s infinite ease-in-out reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    25% { transform: translate(20px, -30px) rotate(5deg); }
    50% { transform: translate(0, -15px) rotate(0deg); }
    75% { transform: translate(-20px, -30px) rotate(-5deg); }
}

/* Media queries for responsive design */
@media (max-width: 768px) {
    .auth-panel {
        max-width: 90%;
    }
    
    .panel-content {
        padding: 1.5rem;
    }
    
    .floating-element {
        display: none;
    }
    
    .logo-text {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .auth-content {
        padding: 1.5rem 1rem;
    }
    
    .panel-content {
        padding: 1.25rem;
    }
    
    .logo-text {
        font-size: 1.3rem;
    }
    
    .robot-head {
        width: 50px;
        height: 40px;
    }
}
</style>
