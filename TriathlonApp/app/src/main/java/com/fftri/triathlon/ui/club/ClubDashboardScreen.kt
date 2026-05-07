package com.fftri.triathlon.ui.club

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
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
import com.fftri.triathlon.ui.components.FftriTopBar
import com.fftri.triathlon.ui.theme.*

@Composable
fun ClubDashboardScreen(
    clubId: Int,
    onAthletes: () -> Unit,
    onRegistrations: () -> Unit,
    onTriathlons: () -> Unit,
    onLogout: () -> Unit
) {
    val vm: ClubViewModel = viewModel()
    val club by vm.club.collectAsStateWithLifecycle()
    val licencies by vm.licencies.collectAsStateWithLifecycle()

    LaunchedEffect(clubId) {
        vm.loadClub(clubId)
        vm.loadLicencies(clubId)
    }

    Scaffold(
        topBar = {
            FftriTopBar(
                title = "Mon Club",
                actions = {
                    TextButton(onClick = onLogout) {
                        Text("Déconnexion", color = Color.White)
                    }
                }
            )
        }
    ) { padding ->
        LazyColumn(
            Modifier.fillMaxSize().padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Club card
            club?.let { c ->
                item {
                    Card(
                        colors = CardDefaults.cardColors(containerColor = FftriBlue),
                        shape  = RoundedCornerShape(16.dp)
                    ) {
                        Column(Modifier.fillMaxWidth().padding(20.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
                            Text(c.name, style = MaterialTheme.typography.headlineMedium, color = Color.White, fontWeight = FontWeight.Bold)
                            Text("${c.city}", color = Color.White.copy(alpha = 0.85f))
                            c.phone?.let { Text("📞 $it", color = Color.White.copy(alpha = 0.85f), style = MaterialTheme.typography.bodySmall) }
                            c.email?.let { Text("✉ $it", color = Color.White.copy(alpha = 0.85f), style = MaterialTheme.typography.bodySmall) }
                        }
                    }
                }
            }

            // Stats
            item {
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    StatCard(modifier = Modifier.weight(1f), label = "Licenciés", value = licencies.size.toString(), color = FftriBlue)
                    StatCard(modifier = Modifier.weight(1f), label = "Compétition", value = licencies.count { it.licenseType == "Compétition" }.toString(), color = FftriRed)
                }
            }

            // Actions
            item {
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    ActionButton("👥 Voir mes licenciés",  FftriBlue,       onClick = onAthletes)
                    ActionButton("📋 Inscriptions du club", FftriLightBlue, onClick = onRegistrations)
                    ActionButton("🏁 Tous les triathlons",  Muted,          onClick = onTriathlons)
                }
            }
        }
    }
}

@Composable
private fun StatCard(modifier: Modifier, label: String, value: String, color: Color) {
    Card(modifier = modifier, shape = RoundedCornerShape(12.dp), colors = CardDefaults.cardColors(containerColor = color)) {
        Column(Modifier.padding(16.dp), horizontalAlignment = Alignment.CenterHorizontally) {
            Text(value, style = MaterialTheme.typography.headlineLarge, color = Color.White, fontWeight = FontWeight.Bold)
            Text(label, style = MaterialTheme.typography.bodySmall, color = Color.White.copy(alpha = 0.85f))
        }
    }
}

@Composable
private fun ActionButton(text: String, color: Color, onClick: () -> Unit) {
    Button(
        onClick = onClick,
        modifier = Modifier.fillMaxWidth().height(52.dp),
        colors  = ButtonDefaults.buttonColors(containerColor = color),
        shape   = RoundedCornerShape(12.dp)
    ) {
        Text(text, fontWeight = FontWeight.SemiBold)
    }
}
