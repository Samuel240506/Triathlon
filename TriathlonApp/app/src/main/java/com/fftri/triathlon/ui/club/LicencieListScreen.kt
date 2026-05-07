package com.fftri.triathlon.ui.club

import androidx.compose.foundation.clickable
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
import com.fftri.triathlon.data.api.model.LicencieDto
import com.fftri.triathlon.ui.components.*
import com.fftri.triathlon.ui.theme.*

@Composable
fun LicencieListScreen(clubId: Int, onBack: () -> Unit, onLicencieClick: (Int) -> Unit) {

    val vm: ClubViewModel = viewModel()
    val licencies by vm.licencies.collectAsStateWithLifecycle()
    val loading   by vm.loading.collectAsStateWithLifecycle()
    val error     by vm.error.collectAsStateWithLifecycle()
    val query     by vm.searchQuery.collectAsStateWithLifecycle()

    LaunchedEffect(clubId) { vm.loadLicencies(clubId) }

    Scaffold(topBar = { FftriTopBar(title = "Licenciés du club", onBack = onBack) }) { padding ->
        Column(Modifier.fillMaxSize().padding(padding)) {
            // Search bar
            OutlinedTextField(
                value = query,
                onValueChange = {
                    vm.setSearch(it)
                    vm.loadLicencies(clubId, it)
                },
                label = { Text("Rechercher...") },
                modifier = Modifier.fillMaxWidth().padding(horizontal = 12.dp, vertical = 8.dp),
                singleLine = true,
                shape = RoundedCornerShape(12.dp)
            )

            when {
                loading -> LoadingScreen()
                error != null -> ErrorMessage(error!!, onRetry = { vm.loadLicencies(clubId) })
                licencies.isEmpty() -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text("Aucun licencié.", color = Muted)
                }
                else -> LazyColumn(contentPadding = PaddingValues(12.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    items(licencies) { l -> LicencieCard(l, onClick = { onLicencieClick(l.id) }) }
                }
            }
        }
    }
}

@Composable
fun LicencieCard(l: LicencieDto, onClick: () -> Unit) {
    Card(
        modifier  = Modifier.fillMaxWidth().clickable(onClick = onClick),
        shape     = RoundedCornerShape(10.dp),
        colors    = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(1.dp)
    ) {
        Row(Modifier.padding(14.dp), verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            // Avatar
            Surface(color = FftriBlue, shape = RoundedCornerShape(50), modifier = Modifier.size(44.dp)) {
                Box(contentAlignment = Alignment.Center) {
                    Text(
                        "${l.firstName.firstOrNull() ?: ""}${l.lastName.firstOrNull() ?: ""}",
                        color = Color.White,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
            Column(Modifier.weight(1f), verticalArrangement = Arrangement.spacedBy(2.dp)) {
                Text(l.fullName, fontWeight = FontWeight.SemiBold, style = MaterialTheme.typography.bodyLarge)
                Text(l.licenseNumber, color = Muted, style = MaterialTheme.typography.bodySmall)
                Text(l.category, color = Muted, style = MaterialTheme.typography.bodySmall)
            }
            Surface(
                color = if (l.licenseType == "Compétition") FftriRed.copy(alpha = 0.12f) else Success.copy(alpha = 0.12f),
                shape = RoundedCornerShape(6.dp)
            ) {
                Text(
                    l.licenseType,
                    color = if (l.licenseType == "Compétition") FftriRed else Success,
                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                    style = MaterialTheme.typography.labelSmall,
                    fontWeight = FontWeight.SemiBold
                )
            }
        }
    }
}
