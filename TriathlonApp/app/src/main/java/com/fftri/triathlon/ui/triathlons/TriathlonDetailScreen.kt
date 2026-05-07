package com.fftri.triathlon.ui.triathlons

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
import com.fftri.triathlon.ui.theme.*

@Composable
fun TriathlonDetailScreen(
    triathlonId: Int,
    licencieId: Int?,
    onBack: () -> Unit,
    onRegistered: () -> Unit
) {
    val vm: TriathlonViewModel = viewModel()
    val tri      by vm.selected.collectAsStateWithLifecycle()
    val results  by vm.registrations.collectAsStateWithLifecycle()
    val loading  by vm.loading.collectAsStateWithLifecycle()
    val error    by vm.error.collectAsStateWithLifecycle()
    val success  by vm.registerSuccess.collectAsStateWithLifecycle()

    var showDialog by remember { mutableStateOf(false) }

    LaunchedEffect(triathlonId) {
        vm.loadTriathlon(triathlonId)
        vm.loadTriathlonRegistrations(triathlonId)
    }

    LaunchedEffect(success) {
        if (success) {
            vm.resetRegisterSuccess()
            onRegistered()
        }
    }

    Scaffold(
        topBar = { FftriTopBar(title = tri?.name ?: "Triathlon", onBack = onBack) },
        floatingActionButton = {
            if (licencieId != null && tri != null) {
                FloatingActionButton(
                    onClick = { showDialog = true },
                    containerColor = FftriBlue
                ) { Text("S'inscrire", color = Color.White, modifier = Modifier.padding(horizontal = 12.dp)) }
            }
        }
    ) { padding ->
        if (loading && tri == null) { LoadingScreen(); return@Scaffold }

        LazyColumn(Modifier.fillMaxSize().padding(padding), contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {

            tri?.let { t ->
                item {
                    Card(colors = CardDefaults.cardColors(containerColor = Color.White), shape = RoundedCornerShape(12.dp), elevation = CardDefaults.cardElevation(2.dp)) {
                        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("Informations", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold, color = FftriBlue)
                            HorizontalDivider()
                            InfoRow("Type",   t.type)
                            InfoRow("Lieu",   t.location)
                            InfoRow("Date",   t.eventDate)
                            t.registrationDeadline?.let { InfoRow("Clôture inscriptions", it) }
                            t.maxParticipants?.let { InfoRow("Participants max", it.toString()) }
                            t.description?.let {
                                Spacer(Modifier.height(4.dp))
                                Text(it, style = MaterialTheme.typography.bodyMedium, color = Muted)
                            }
                        }
                    }
                }

                if (results.isNotEmpty()) {
                    item { SectionTitle("Inscriptions (${results.size})") }
                    items(results) { reg ->
                        Card(colors = CardDefaults.cardColors(containerColor = Color.White), shape = RoundedCornerShape(8.dp)) {
                            Row(
                                Modifier.fillMaxWidth().padding(12.dp),
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Text(reg.licencie?.fullName ?: "Licencié #${reg.licencieId}", style = MaterialTheme.typography.bodyMedium)
                                StatusBadge(reg.status)
                            }
                        }
                    }
                }

                error?.let {
                    item { ErrorMessage(it) }
                }
            }
        }
    }

    if (showDialog && licencieId != null) {
        AlertDialog(
            onDismissRequest = { showDialog = false },
            title = { Text("Confirmer l'inscription") },
            text  = { Text("Voulez-vous vous inscrire à ${tri?.name} ?") },
            confirmButton = {
                Button(
                    onClick = { showDialog = false; vm.register(triathlonId, licencieId) },
                    colors  = ButtonDefaults.buttonColors(containerColor = FftriBlue)
                ) { Text("Confirmer") }
            },
            dismissButton = {
                OutlinedButton(onClick = { showDialog = false }) { Text("Annuler") }
            }
        )
    }
}
