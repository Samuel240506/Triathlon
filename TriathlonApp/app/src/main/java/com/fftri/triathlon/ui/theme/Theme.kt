package com.fftri.triathlon.ui.theme

import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val ColorScheme = lightColorScheme(
    primary         = FftriBlue,
    onPrimary       = White,
    primaryContainer = FftriLightBlue,
    secondary       = FftriRed,
    onSecondary     = White,
    background      = SurfaceLight,
    surface         = White,
    onSurface       = OnSurface,
    surfaceVariant  = Color(0xFFE8EDF7),
    outline         = Color(0xFFD1D5DB),
)

@Composable
fun TriathlonTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = ColorScheme,
        typography  = Typography,
        content     = content
    )
}
