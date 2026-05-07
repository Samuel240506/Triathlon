package com.fftri.triathlon.ui.registrations

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import com.fftri.triathlon.data.api.model.RegistrationDto
import com.fftri.triathlon.ui.components.*
import com.fftri.triathlon.ui.theme.*

@Composable
fun MyRegistrationsScreen(licencieId: Int, onBack: () -> Unit) {

    val vm: RegistrationViewModel = viewModel()
    val registrations by vm.registrations.collectAsStateWithLifecycle()
    val loading       by vm.loading.collectAsStateWithLifecycle()
    val error         by vm.error.collectAsStateWithLifecycle()

    LaunchedEffect(licencieId) { vm.loadMyRegistrations(licencieId) }

    Scaffold(topBar = { FftriTopBar(title = "Mes inscriptions", onBack = onBack) }) { padding ->
        Column(Modifier.fillMaxSize().padding(padding)) {
            when {
                loading -> LoadingScreen()
                error != null -> ErrorMessage(error!!, onRetry = { vm.loadMyRegistrations(licencieId) })
                registrations.isEmpty() -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text("Aucune inscription.", color = Muted)
                }
                else -> LazyColumn(contentPadding = PaddingValues(12.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    items(registrations) { reg -> RegistrationCard(reg) }
                }
            }
        }
    }
}

@Composable
fun ClubRegistrationsScreen(onBack: () -> Unit) {

    val vm: RegistrationViewModel = viewModel()
    val registrations by vm.registrations.collectAsStateWithLifecycle()
    val loading       by vm.loading.collectAsStateWithLifecycle()
    val error         by vm.error.collectAsStateWithLifecycle()

    LaunchedEffect(Unit) { vm.loadClubRegistrations(emptyList()) }

    Scaffold(topBar = { FftriTopBar(title = "Inscriptions du club", onBack = onBack) }) { padding ->
        Column(Modifier.fillMaxSize().padding(padding)) {
            when {
                loading -> LoadingScreen()
                error != null -> ErrorMessage(error!!)
                registrations.isEmpty() -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text("Aucune inscription.", color = Muted)
                }
                else -> LazyColumn(contentPadding = PaddingValues(12.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    items(registrations) { reg -> RegistrationCard(reg) }
                }
            }
        }
    }
}

@Composable
private fun RegistrationCard(reg: RegistrationDto) {
    Card(
        shape  = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(2.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                Text(
                    reg.triathlon?.name ?: "Triathlon #${reg.triathlonId}",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.weight(1f)
                )
                StatusBadge(reg.status)
            }
            reg.triathlon?.let {
                Text("📅 ${it.eventDate}", style = MaterialTheme.typography.bodyMedium, color = Muted)
                Text("📍 ${it.location}", style = MaterialTheme.typography.bodyMedium, color = Muted)
            }
            reg.licencie?.let {
                Text("👤 ${it.fullName}", style = MaterialTheme.typography.bodySmall, color = Muted)
            }
            Text("Inscrit le ${reg.registrationDate.take(10)}", style = MaterialTheme.typography.bodySmall, color = Muted)
        }
    }
}
