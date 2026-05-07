package com.fftri.triathlon.ui.club

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import com.fftri.triathlon.ui.components.*
import com.fftri.triathlon.ui.registrations.RegistrationViewModel
import com.fftri.triathlon.ui.theme.*

@Composable
fun LicencieDetailScreen(licencieId: Int, onBack: () -> Unit) {

    val clubVm: ClubViewModel = viewModel()
    val regVm: RegistrationViewModel = viewModel()

    val licencie      by clubVm.selected.collectAsStateWithLifecycle()
    val registrations by regVm.registrations.collectAsStateWithLifecycle()
    val loading       by clubVm.loading.collectAsStateWithLifecycle()

    LaunchedEffect(licencieId) {
        clubVm.loadLicencie(licencieId)
        regVm.loadMyRegistrations(licencieId)
    }

    Scaffold(topBar = { FftriTopBar(title = licencie?.fullName ?: "Athlète", onBack = onBack) }) { padding ->
        if (loading && licencie == null) { LoadingScreen(); return@Scaffold }

        LazyColumn(Modifier.fillMaxSize().padding(padding), contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {

            licencie?.let { l ->
                item {
                    Card(colors = CardDefaults.cardColors(containerColor = Color.White), shape = RoundedCornerShape(12.dp), elevation = CardDefaults.cardElevation(2.dp)) {
                        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("Informations", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold, color = FftriBlue)
                            HorizontalDivider()
                            InfoRow("N° Licence",     l.licenseNumber)
                            InfoRow("Nom",            l.fullName)
                            InfoRow("Genre",          if (l.gender == "M") "Masculin" else "Féminin")
                            InfoRow("Catégorie",      l.category)
                            InfoRow("Type licence",   l.licenseType)
                            l.birthDate?.let { InfoRow("Date naissance", it) }
                            l.email?.let     { InfoRow("Email",          it) }
                            l.phone?.let     { InfoRow("Téléphone",      it) }
                        }
                    }
                }
            }

            if (registrations.isNotEmpty()) {
                item { SectionTitle("Inscriptions aux triathlons (${registrations.size})") }
                items(registrations) { reg ->
                    Card(
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        shape = RoundedCornerShape(10.dp),
                        elevation = CardDefaults.cardElevation(1.dp),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            Modifier.fillMaxWidth().padding(14.dp),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Column(Modifier.weight(1f), verticalArrangement = Arrangement.spacedBy(2.dp)) {
                                Text(
                                    reg.triathlon?.name ?: "Triathlon #${reg.triathlonId}",
                                    fontWeight = FontWeight.SemiBold,
                                    style = MaterialTheme.typography.bodyMedium
                                )
                                reg.triathlon?.let {
                                    Text("${it.eventDate} — ${it.location}", style = MaterialTheme.typography.bodySmall, color = Muted)
                                }
                            }
                            StatusBadge(reg.status)
                        }
                    }
                }
            } else {
                item {
                    Text("Aucune inscription.", color = Muted, style = MaterialTheme.typography.bodyMedium, modifier = Modifier.padding(8.dp))
                }
            }
        }
    }
}
