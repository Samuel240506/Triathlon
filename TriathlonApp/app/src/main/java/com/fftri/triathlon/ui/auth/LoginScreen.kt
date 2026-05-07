package com.fftri.triathlon.ui.auth

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import com.fftri.triathlon.ui.theme.*

@Composable
fun LoginScreen(onLoginSuccess: () -> Unit) {

    val vm: LoginViewModel = viewModel()
    val state by vm.state.collectAsStateWithLifecycle()

    var email    by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var showPass by remember { mutableStateOf(false) }

    LaunchedEffect(state) {
        if (state is LoginState.Success) onLoginSuccess()
    }

    Box(
        Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(FftriDarkBlue, FftriBlue)))
    ) {
        Column(
            Modifier.align(Alignment.Center).padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(20.dp)
        ) {
            // Logo / Header
            Text("FFTRI", color = Color.White, fontSize = 40.sp, fontWeight = FontWeight.ExtraBold)
            Text(
                "Fédération Française de Triathlon",
                color = Color.White.copy(alpha = 0.8f),
                fontSize = 14.sp
            )

            Spacer(Modifier.height(8.dp))

            // Card formulaire
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape  = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    Modifier.padding(24.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    Text(
                        "Connexion",
                        style = MaterialTheme.typography.headlineMedium,
                        fontWeight = FontWeight.Bold,
                        color = FftriBlue
                    )

                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it },
                        label = { Text("Adresse email") },
                        modifier = Modifier.fillMaxWidth(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                        singleLine = true
                    )

                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Mot de passe") },
                        modifier = Modifier.fillMaxWidth(),
                        visualTransformation = if (showPass) VisualTransformation.None else PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                        trailingIcon = {
                            TextButton(onClick = { showPass = !showPass }) {
                                Text(if (showPass) "Masquer" else "Voir", fontSize = 11.sp)
                            }
                        },
                        singleLine = true
                    )

                    if (state is LoginState.Error) {
                        Text(
                            (state as LoginState.Error).message,
                            color = MaterialTheme.colorScheme.error,
                            style = MaterialTheme.typography.bodySmall
                        )
                    }

                    Button(
                        onClick = { vm.login(email, password) },
                        modifier = Modifier.fillMaxWidth().height(48.dp),
                        enabled = state !is LoginState.Loading,
                        colors  = ButtonDefaults.buttonColors(containerColor = FftriBlue)
                    ) {
                        if (state is LoginState.Loading) {
                            CircularProgressIndicator(Modifier.size(20.dp), color = Color.White, strokeWidth = 2.dp)
                        } else {
                            Text("Se connecter", fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }
        }
    }
}
