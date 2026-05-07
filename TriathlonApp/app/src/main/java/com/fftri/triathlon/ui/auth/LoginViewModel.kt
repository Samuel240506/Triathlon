package com.fftri.triathlon.ui.auth

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.fftri.triathlon.TriathlonApplication
import com.fftri.triathlon.data.api.ApiClient
import com.fftri.triathlon.data.api.model.LoginRequest
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

sealed class LoginState {
    object Idle : LoginState()
    object Loading : LoginState()
    object Success : LoginState()
    data class Error(val message: String) : LoginState()
}

class LoginViewModel(app: Application) : AndroidViewModel(app) {

    private val session = (app as TriathlonApplication).sessionManager
    private val api     = ApiClient.service

    private val _state = MutableStateFlow<LoginState>(LoginState.Idle)
    val state: StateFlow<LoginState> = _state

    fun login(email: String, password: String) {
        if (email.isBlank() || password.isBlank()) {
            _state.value = LoginState.Error("Email et mot de passe requis.")
            return
        }
        viewModelScope.launch {
            _state.value = LoginState.Loading
            try {
                val response = api.login(LoginRequest(email.trim(), password))
                if (response.isSuccessful) {
                    val body = response.body()!!
                    ApiClient.setToken(body.token)
                    session.saveSession(
                        token  = body.token,
                        userId = body.user.id,
                        name   = body.user.name,
                        email  = body.user.email,
                        role   = body.user.role,
                        clubId = body.user.clubId
                    )
                    _state.value = LoginState.Success
                } else {
                    _state.value = LoginState.Error("Identifiants incorrects.")
                }
            } catch (e: Exception) {
                _state.value = LoginState.Error("Erreur réseau : ${e.message}")
            }
        }
    }
}
