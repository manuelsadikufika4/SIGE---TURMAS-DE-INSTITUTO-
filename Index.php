<!-- Splash Screen -->
<div id="splash-screen">
    <div class="splash-content">
        <div class="logo-f">SIGE - TURMAS</div>
        
        <div class="loading-container">
            <div class="dot-base"></div>
            <div class="progress-fill"></div>
        </div>
        <p>A Carregar...</p>
    </div>
</div>

<style>
#splash-screen {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    font-family: sans-serif;
}

.logo-f {
    font-size: 80px;
    font-weight: bold;
    color: #1877f2;
    margin-bottom: 20px;
}

.loading-container {
    width: 120px;
    height: 6px;
    background: #e4e6eb;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    margin: 0 auto;
}

.progress-fill {
    width: 0%;
    height: 100%;
    background: #1877f2;
    border-radius: 10px;
    /* A animação dura 2.5s para terminar um pouco antes do redirecionamento */
    animation: fillBar 2.5s ease-in-out forwards;
}

@keyframes fillBar {
    0% { width: 0%; }
    30% { width: 40%; }
    70% { width: 80%; }
    100% { width: 100%; }
}

.splash-content { text-align: center; }

p {
    color: #65676b;
    font-size: 14px;
    margin-top: 10px;
}
</style>
<script>
setTimeout(() => {
    window.location.href = 'login.php';
}, 3000);
</script>
