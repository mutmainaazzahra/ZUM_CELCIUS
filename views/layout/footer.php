</div> 

<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    body>.container-fluid,
    body>.container {
        flex: 1;
    }

    .footer-glass {
        background: #ffffff;
        border-top: 2px solid var(--tertiary);
        color: var(--paragraph);
        padding: 1.5rem 0;
        margin-top: auto;
        position: relative;
        z-index: 10;
        box-shadow: 0 -4px 20px rgba(39, 35, 67, 0.05);
    }

    .footer-link {
        color: var(--headline);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .footer-link:hover {
        color: var(--button);
        text-decoration: underline;
    }
</style>

<footer class="footer-glass text-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start mb-2 mb-md-0">
                <p class="mb-0 small fw-medium">
                    &copy; <?php echo date('Y'); ?> <strong>Zum Celcius</strong>.
                    <span class="text-muted ms-1" style="font-size: 0.85em;">Weather App Project.</span>
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 small">
                    Ditenagai oleh <a href="https://openweathermap.org/" target="_blank" class="footer-link">OpenWeather</a>
                    & <a href="https://www.mapbox.com/" target="_blank" class="footer-link">Mapbox</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>