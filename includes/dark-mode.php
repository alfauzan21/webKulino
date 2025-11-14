<!-- Dark Mode Toggle -->
<style>
    /* Default light */
    body {
        transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
        background: #121212 !important;
        color: #e9ecef !important;
    }
    body.dark-mode .card {
        background: #1e1e1e !important;
        color: #f8f9fa !important;
    }
    body.dark-mode .table thead {
        background: linear-gradient(135deg, #6610f2, #0d6efd) !important;
    }
    body.dark-mode .table-hover tbody tr:hover {
        background-color: #2a2a2a !important;
    }
    .toggle-btn {
        cursor: pointer;
        border: none;
        background: transparent;
        font-size: 1.5rem;
    }
</style>

<button id="darkToggle" class="toggle-btn me-3">🌙</button>

<script>
    const toggleBtn = document.getElementById("darkToggle");
    const body = document.body;

    toggleBtn.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        toggleBtn.textContent = body.classList.contains("dark-mode") ? "☀️" : "🌙";
        localStorage.setItem("darkMode", body.classList.contains("dark-mode"));
    });

    // Load mode dari localStorage
    if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
        toggleBtn.textContent = "☀️";
    }
</script>
