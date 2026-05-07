package com.fftri.triathlon.ui.triathlons

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import com.fftri.triathlon.data.api.model.TriathlonDto
import com.fftri.triathlon.ui.components.*
import com.fftri.triathlon.ui.theme.*

@Composable
fun TriathlonListScreen(
    onTriathlonClick: (Int) -> Unit,
    onLogout: () -> Unit
) {
    val vm: TriathlonViewModel = viewModel()
    val triathlons by vm.triathlons.collectAsStateWithLifecycle()
    val loading    by vm.loading.collectAsStateWithLifecycle()
    val error      by vm.error.collectAsStateWithLifecycle()

    LaunchedEffect(Unit) { vm.loadTriathlons() }

    Scaffold(
        topBar = {
            FftriTopBar(
                title = "Triathlons FFTRI",
                actions = {
                    TextButton(onClick = onLogout) {
                        Text("Déconnexion", color = androidx.compose.ui.graphics.Color.White)
                    }
                }
            )
        }
    ) { padding ->
        Column(Modifier.fillMaxSize().padding(padding)) {
            when {
                loading -> LoadingScreen()
                error != null -> ErrorMessage(error!!, onRetry = { vm.loadTriathlons() })
                triathlons.isEmpty() -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text("Aucun triathlon disponible.", color = Muted)
                }
                else -> LazyColumn(contentPadding = PaddingValues(12.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    items(triathlons) { tri ->
                        TriathlonCard(tri, onClick = { onTriathlonClick(tri.id) })
                    }
                }
            }
        }
    }
}

@Composable
fun TriathlonCard(tri: TriathlonDto, onClick: () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth().clickable(onClick = onClick),
        shape  = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = androidx.compose.ui.graphics.Color.White),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
            Row(
                Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(tri.name, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold, modifier = Modifier.weight(1f))
                Surface(color = FftriBlue, shape = RoundedCornerShape(6.dp)) {
                    Text(tri.type, color = androidx.compose.ui.graphics.Color.White, modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp), style = MaterialTheme.typography.labelSmall)
                }
            }
            Text("📍 ${tri.location}", style = MaterialTheme.typography.bodyMedium, color = Muted)
            Text("📅 ${tri.eventDate}", style = MaterialTheme.typography.bodyMedium, color = Muted)
            tri.maxParticipants?.let {
                Text("👥 $it participants max", style = MaterialTheme.typography.bodySmall, color = Muted)
            }
        }
    }
}
