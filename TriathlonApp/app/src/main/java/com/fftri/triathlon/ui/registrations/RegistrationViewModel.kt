package com.fftri.triathlon.ui.registrations

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.fftri.triathlon.data.api.ApiClient
import com.fftri.triathlon.data.api.model.RegistrationDto
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class RegistrationViewModel : ViewModel() {

    private val api = ApiClient.service

    private val _registrations = MutableStateFlow<List<RegistrationDto>>(emptyList())
    val registrations: StateFlow<List<RegistrationDto>> = _registrations

    private val _loading = MutableStateFlow(false)
    val loading: StateFlow<Boolean> = _loading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    fun loadMyRegistrations(licencieId: Int) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.getRegistrations(licencieId = licencieId)
                if (r.isSuccessful) _registrations.value = r.body() ?: emptyList()
                else _error.value = "Erreur lors du chargement."
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }

    fun loadClubRegistrations(clubLicencieIds: List<Int>) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.getRegistrations()
                if (r.isSuccessful) _registrations.value = r.body() ?: emptyList()
                else _error.value = "Erreur lors du chargement."
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }
}
