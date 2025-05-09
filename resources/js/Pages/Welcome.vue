<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import ChatBot from '@/Components/ChatBot.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Animation variables
const animationActive = ref(false);

// Robot animation class
const robotAnimationClass = ref('');

// Manage the startup animation sequence
onMounted(() => {
    setTimeout(() => {
        animationActive.value = true;
    }, 500);

    // Trigger robot animation periodically
    setInterval(() => {
        robotAnimationClass.value = 'robot-anim';
        setTimeout(() => {
            robotAnimationClass.value = '';
        }, 2000);
    }, 8000);
});
</script>

<template>
    <Head title="AssistActesBot" />
    <div class="welcome-container">
        <!-- Background elements -->
        <div class="circuit-bg"></div>
        <div class="grid-lines"></div>
        
        <!-- Floating data screens -->
        <div class="data-screen left-screen">
            <div class="screen-header">
                <div class="screen-indicator"></div>
                <span>ACTES</span>
            </div>
            <div class="data-content">
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
            </div>
        </div>
        
        <div class="data-screen right-screen">
            <div class="screen-header">
                <div class="screen-indicator"></div>
                <span>ACTIVITATS</span>
            </div>
            <div class="data-content">
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
                <div class="data-line"></div>
            </div>
        </div>
        
        <!-- Institutional shield -->
        <div class="institutional-shield">
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
        </div>
        
        <!-- Main content container -->
        <div class="content-container" :class="{ 'active': animationActive }">
            <!-- Robot visual representation -->
            <div class="robot-container" :class="robotAnimationClass">
                <div class="robot-head">
                    <div class="robot-eyes">
                        <div class="robot-eye left"></div>
                        <div class="robot-eye right"></div>
                    </div>
                    <div class="robot-antenna"></div>
                    <div class="robot-mouth"></div>
                </div>
                <div class="robot-body">
                    <div class="robot-panel">
                        <div class="robot-light"></div>
                        <div class="robot-light"></div>
                        <div class="robot-light"></div>
                    </div>
                </div>
                <div class="robot-base">
                    <div class="hologram-projector"></div>
                </div>
            </div>
            
            <!-- Main title -->
            <h1 class="title">AssistActesBot</h1>
            
            <!-- Description -->
            <div class="description">
                <p>El vostre assistent virtual per a la gestió d'actes institucionals</p>
            </div>
            
            <!-- Holographic interface elements -->
            <div class="hologram-interface">
                <div class="interface-element"></div>
                <div class="interface-element"></div>
                <div class="interface-element"></div>
                <div class="interface-circle"></div>
            </div>
            
            <!-- Call to action -->
            <div class="cta-container">
                <p>Feu clic al botó de xat per començar una conversa</p>
                <div class="arrow-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 19V5M5 12l7-7 7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Navigation links -->
            <nav v-if="canLogin" class="login-links">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="nav-link dashboard-link"
                >
                    Tauler de control
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="nav-link login-link"
                    >
                        Iniciar sessió
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="nav-link register-link"
                    >
                        Registre
                    </Link>
                </template>
            </nav>
        </div>
        
        <!-- Version info with modern styling -->
        <div class="version-info">
            Laravel v{{ laravelVersion }} | PHP v{{ phpVersion }}
        </div>
        
        <!-- Include the chatbot component -->
        <ChatBot />
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700&family=Roboto:wght@300;400;500&display=swap');

/* Base styling */
.welcome-container {
    min-height: 100vh;
    width: 100%;
    background-color: #0a0f25;
    color: white;
    font-family: 'Exo 2', sans-serif;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
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

/* Main content container */
.content-container {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    max-width: 1200px;
    width: 100%;
    padding: 2rem;
    transform: translateY(30px);
    opacity: 0;
    transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.content-container.active {
    transform: translateY(0);
    opacity: 1;
}

/* Institutional shield */
.institutional-shield {
    position: absolute;
    top: 20px;
    left: 20px;
    width: 60px;
    height: 60px;
    z-index: 2;
    opacity: 0.8;
    filter: drop-shadow(0 0 10px rgba(0, 102, 204, 0.8));
    animation: pulse 5s infinite alternate;
}

@keyframes pulse {
    0% { filter: drop-shadow(0 0 10px rgba(0, 102, 204, 0.5)); }
    100% { filter: drop-shadow(0 0 15px rgba(0, 102, 204, 0.9)); }
}

/* Robot styling */
.robot-container {
    width: 260px;
    height: 260px;
    margin-bottom: 3rem;
    position: relative;
    transition: transform 0.5s ease;
}

.robot-container.robot-anim {
    animation: robotMove 2s ease-in-out;
}

@keyframes robotMove {
    0% { transform: translateY(0); }
    30% { transform: translateY(-15px); }
    40% { transform: translateY(-15px) rotate(-5deg); }
    60% { transform: translateY(-15px) rotate(5deg); }
    70% { transform: translateY(-15px); }
    100% { transform: translateY(0); }
}

.robot-head {
    width: 120px;
    height: 100px;
    background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
    border-radius: 25px 25px 15px 15px;
    position: absolute;
    top: 20px;
    left: 70px;
    box-shadow: 0 0 20px rgba(0, 102, 204, 0.7);
    overflow: hidden;
    z-index: 2;
}

.robot-head::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 10px;
    background: rgba(255, 255, 255, 0.1);
    top: 0;
    left: 0;
    border-radius: 25px 25px 0 0;
}

.robot-eyes {
    display: flex;
    justify-content: space-around;
    padding-top: 25px;
}

.robot-eye {
    width: 25px;
    height: 25px;
    background: white;
    border-radius: 50%;
    position: relative;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
}

.robot-eye::after {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
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
    width: 40px;
    height: 8px;
    background: rgba(255, 255, 255, 0.7);
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 10px;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.robot-antenna {
    width: 8px;
    height: 25px;
    background: #A0A0A0;
    position: absolute;
    top: -23px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
}

.robot-antenna::after {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    background: #FF3366;
    border-radius: 50%;
    top: -7px;
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 0 10px #FF3366;
    animation: antennaGlow 1.5s infinite alternate;
}

@keyframes antennaGlow {
    from { box-shadow: 0 0 10px #FF3366; }
    to { box-shadow: 0 0 20px #FF3366; }
}

.robot-body {
    width: 150px;
    height: 110px;
    background: linear-gradient(to bottom, #0066CC, #004080);
    position: absolute;
    top: 110px;
    left: 55px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 64, 128, 0.7);
    overflow: hidden;
    z-index: 1;
}

.robot-body::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
    animation: bodyShine 5s infinite;
}

@keyframes bodyShine {
    0% { transform: translateX(-100%) translateY(-100%); }
    50% { transform: translateX(100%) translateY(100%); }
    100% { transform: translateX(-100%) translateY(-100%); }
}

.robot-panel {
    width: 80px;
    height: 30px;
    background: rgba(0, 0, 0, 0.2);
    position: absolute;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 5px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 0 10px;
}

.robot-light {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00FF9B;
    animation: lightBlink 2s infinite alternate;
}

.robot-light:nth-child(2) {
    animation-delay: 0.7s;
    background: #FFD700;
}

.robot-light:nth-child(3) {
    animation-delay: 1.3s;
    background: #FF3366;
}

@keyframes lightBlink {
    0%, 30% { opacity: 0.4; }
    70%, 100% { opacity: 1; }
}

.robot-base {
    width: 180px;
    height: 25px;
    background: linear-gradient(to bottom, #004080, #002040);
    position: absolute;
    top: 220px;
    left: 40px;
    border-radius: 0 0 50px 50px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hologram-projector {
    width: 60px;
    height: 10px;
    background: rgba(0, 102, 204, 0.5);
    border-radius: 5px;
    box-shadow: 0 0 15px rgba(0, 102, 204, 0.8);
}

.hologram-projector::before,
.hologram-projector::after {
    content: '';
    position: absolute;
    width: 180px;
    height: 40px;
    background: rgba(0, 102, 204, 0.05);
    transform-origin: center;
    transform: translateY(-130px);
    border-radius: 50%;
    filter: blur(5px);
    animation: hologramMove 10s infinite ease-in-out;
}

.hologram-projector::after {
    width: 140px;
    height: 30px;
    opacity: 0.8;
    animation-delay: -5s;
}

@keyframes hologramMove {
    0%, 100% { transform: translateY(-130px) scale(1, 0.2); opacity: 0.05; }
    50% { transform: translateY(-150px) scale(1.1, 0.3); opacity: 0.1; }
}

/* Title styling */
.title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    background: linear-gradient(to right, #FFFFFF 0%, #0066CC 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    position: relative;
    text-shadow: 0 2px 10px rgba(0, 102, 204, 0.4);
}

.title::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 3px;
    background: linear-gradient(to right, transparent, #0066CC, transparent);
    bottom: -10px;
    left: 0;
}

/* Description */
.description {
    font-size: 1.2rem;
    font-weight: 300;
    margin-bottom: 2.5rem;
    color: #E0E6FF;
    max-width: 600px;
    line-height: 1.6;
}

/* Holographic interface elements */
.hologram-interface {
    width: 100%;
    max-width: 500px;
    height: 80px;
    position: relative;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.interface-element {
    width: 120px;
    height: 40px;
    background: rgba(0, 102, 204, 0.15);
    border: 1px solid rgba(0, 102, 204, 0.3);
    border-radius: 5px;
    position: relative;
    overflow: hidden;
}

.interface-element::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 102, 204, 0.2), transparent);
    animation: shimmer 3s infinite;
}

.interface-element::after {
    content: '';
    position: absolute;
    width: 80%;
    height: 4px;
    background: #0066CC;
    top: 50%;
    left: 10%;
    transform: translateY(-50%);
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(0, 102, 204, 0.8);
}

.interface-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid rgba(0, 102, 204, 0.5);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.interface-circle::before, 
.interface-circle::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(0, 102, 204, 0.2);
}

.interface-circle::before {
    width: 40px;
    height: 40px;
    animation: pulse 2s infinite alternate;
}

.interface-circle::after {
    width: 20px;
    height: 20px;
    background: #0066CC;
    animation: pulse 2s infinite alternate-reverse;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Call to action */
.cta-container {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.cta-container p {
    font-size: 1.1rem;
    color: #A0D2FF;
    margin-bottom: 1rem;
}

.arrow-indicator {
    width: 40px;
    height: 40px;
    margin: 0 auto;
    color: #0066CC;
    animation: bounceArrow 2s infinite;
}

@keyframes bounceArrow {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-15px); }
    60% { transform: translateY(-7px); }
}

/* Navigation links */
.login-links {
    display: flex;
    gap: 1.5rem;
    margin-top: 1rem;
}

.nav-link {
    padding: 0.6rem 1.2rem;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: all 0.5s;
}

.nav-link:hover::before {
    left: 100%;
}

.dashboard-link {
    background: rgba(0, 102, 204, 0.2);
    border: 1px solid #0066CC;
    color: white;
}

.dashboard-link:hover {
    background: rgba(0, 102, 204, 0.3);
    box-shadow: 0 0 15px rgba(0, 102, 204, 0.5);
}

.login-link {
    background: rgba(0, 102, 204, 0.2);
    border: 1px solid #0066CC;
    color: white;
}

.login-link:hover {
    background: rgba(0, 102, 204, 0.3);
    box-shadow: 0 0 15px rgba(0, 102, 204, 0.5);
}

.register-link {
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    background: transparent;
}

.register-link:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Data screens */
.data-screen {
    position: absolute;
    width: 280px;
    height: 200px;
    background: rgba(10, 15, 37, 0.7);
    border: 1px solid rgba(0, 102, 204, 0.5);
    border-radius: 8px;
    padding: 1rem;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    backdrop-filter: blur(5px);
    box-shadow: 0 0 20px rgba(0, 102, 204, 0.3);
}

.left-screen {
    left: 5%;
    top: 25%;
    transform: rotate(-5deg);
}

.right-screen {
    right: 5%;
    top: 25%;
    transform: rotate(5deg);
}

.screen-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0, 102, 204, 0.3);
    padding-bottom: 0.5rem;
}

.screen-indicator {
    width: 10px;
    height: 10px;
    background: #0066CC;
    border-radius: 50%;
    margin-right: 0.5rem;
    box-shadow: 0 0 10px #0066CC;
    animation: blink 2s infinite alternate;
}

.screen-header span {
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 1px;
    color: #0066CC;
}

.data-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.data-line {
    height: 10px;
    background: rgba(0, 102, 204, 0.1);
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.data-line::before {
    content: '';
    position: absolute;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 102, 204, 0.5), transparent);
    animation: dataFlow 3s infinite;
    width: 50%;
}

.data-line:nth-child(2n)::before {
    animation-delay: 1.5s;
}

.data-line:nth-child(3n)::before {
    animation-delay: 0.7s;
}

.data-line:nth-child(4n)::before {
    animation-delay: 2.1s;
}

@keyframes dataFlow {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes blink {
    0%, 80% { opacity: 0.7; }
    90%, 100% { opacity: 1; }
}

/* Version info */
.version-info {
    position: absolute;
    bottom: 1rem;
    text-align: center;
    font-size: 0.8rem;
    opacity: 0.6;
    color: #A0D2FF;
    z-index: 2;
}

/* Media queries for responsive design */
@media (max-width: 768px) {
    .title {
        font-size: 2.5rem;
    }
    
    .description {
        font-size: 1rem;
    }
    
    .robot-container {
        transform: scale(0.8);
        margin-bottom: 1.5rem;
    }
    
    .hologram-interface {
        max-width: 300px;
        height: 60px;
    }
    
    .interface-element {
        width: 80px;
        height: 30px;
    }
    
    .interface-circle {
        width: 40px;
        height: 40px;
    }
    
    .data-screen {
        display: none;
    }
}

@media (max-width: 480px) {
    .title {
        font-size: 2rem;
    }
    
    .description {
        font-size: 0.9rem;
    }
    
    .robot-container {
        transform: scale(0.7);
        margin-bottom: 1rem;
    }
    
    .hologram-interface {
        max-width: 250px;
        height: 50px;
    }
    
    .login-links {
        flex-direction: column;
        gap: 0.8rem;
    }
}
</style>
