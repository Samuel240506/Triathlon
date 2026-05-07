package com.fftri.triathlon.ui.triathlons

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.fftri.triathlon.data.api.ApiClient
import com.fftri.triathlon.data.api.model.RegistrationDto
import com.fftri.triathlon.data.api.model.TriathlonDto
import com.fftri.triathlon.data.api.model.CreateRegistrationRequest
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class TriathlonViewModel : ViewModel() {

    private val api = ApiClient.service

    private val _triathlons = MutableStateFlow<List<TriathlonDto>>(emptyList())
    val triathlons: StateFlow<List<TriathlonDto>> = _triathlons

    private val _selected = MutableStateFlow<TriathlonDto?>(null)
    val selected: StateFlow<TriathlonDto?> = _selected

    private val _registrations = MutableStateFlow<List<RegistrationDto>>(emptyList())
    val registrations: StateFlow<List<RegistrationDto>> = _registrations

    private val _loading = MutableStateFlow(false)
    val loading: StateFlow<Boolean> = _loading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    private val _registerSuccess = MutableStateFlow(false)
    val registerSuccess: StateFlow<Boolean> = _registerSuccess

    fun loadTriathlons() {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.getTriathlons()
                if (r.isSuccessful) _triathlons.value = r.body() ?: emptyList()
                else _error.value = "Erreur lors du chargement des triathlons."
            } catch (e: Exception) {
                _error.value = "Erreur réseau : ${e.message}"
            } finally {
                _loading.value = false
            }
        }
    }

    fun loadTriathlon(id: Int) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.getTriathlon(id)
                if (r.isSuccessful) _selected.value = r.body()
                else _error.value = "Triathlon introuvable."
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }

    fun loadTriathlonRegistrations(triathlonId: Int) {
        viewModelScope.launch {
            try {
                val r = api.getRegistrations(triathlonId = triathlonId)
                if (r.isSuccessful) _registrations.value = r.body() ?: emptyList()
            } catch (_: Exception) {}
        }
    }

    fun register(triathlonId: Int, licencieId: Int) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.createRegistration(CreateRegistrationRequest(triathlonId, licencieId))
                if (r.isSuccessful) {
                    _registerSuccess.value = true
                } else {
                    val code = r.code()
                    _error.value = when (code) {
                        409  -> "Vous êtes déjà inscrit à ce triathlon."
                        403  -> "Accès refusé."
                        else -> "Erreur lors de l'inscription ($code)."
                    }
                }
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }

    fun resetRegisterSuccess() { _registerSuccess.value = false }
    fun clearError() { _error.value = null }
}
