package com.fftri.triathlon.ui.club

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.fftri.triathlon.data.api.ApiClient
import com.fftri.triathlon.data.api.model.ClubDto
import com.fftri.triathlon.data.api.model.LicencieDto
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class ClubViewModel : ViewModel() {

    private val api = ApiClient.service

    private val _club = MutableStateFlow<ClubDto?>(null)
    val club: StateFlow<ClubDto?> = _club

    private val _licencies = MutableStateFlow<List<LicencieDto>>(emptyList())
    val licencies: StateFlow<List<LicencieDto>> = _licencies

    private val _selected = MutableStateFlow<LicencieDto?>(null)
    val selected: StateFlow<LicencieDto?> = _selected

    private val _loading = MutableStateFlow(false)
    val loading: StateFlow<Boolean> = _loading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    private var _searchQuery = MutableStateFlow("")
    val searchQuery: StateFlow<String> = _searchQuery

    fun loadClub(clubId: Int) {
        viewModelScope.launch {
            try {
                val r = api.getClub(clubId)
                if (r.isSuccessful) _club.value = r.body()
            } catch (_: Exception) {}
        }
    }

    fun loadLicencies(clubId: Int, query: String = "") {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val r = api.getLicencies(clubId = clubId, search = query.ifBlank { null })
                if (r.isSuccessful) _licencies.value = r.body() ?: emptyList()
                else _error.value = "Erreur lors du chargement."
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }

    fun loadLicencie(id: Int) {
        viewModelScope.launch {
            _loading.value = true
            try {
                val r = api.getLicencie(id)
                if (r.isSuccessful) _selected.value = r.body()
                else _error.value = "Licencié introuvable."
            } catch (e: Exception) {
                _error.value = "Erreur réseau."
            } finally {
                _loading.value = false
            }
        }
    }

    fun setSearch(q: String) { _searchQuery.value = q }
}
