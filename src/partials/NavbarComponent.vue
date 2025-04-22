<script setup>
import { RouterLink } from 'vue-router';

</script>

<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Anoltiga</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            SMS
                        </a>
                        <ul class="dropdown-menu">
                            <li><RouterLink class="dropdown-item" to="/sms/sender">Sender</RouterLink></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><RouterLink class="dropdown-item" to="/sms/blast">Blast</RouterLink></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>