package com.fftri.triathlon

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.fftri.triathlon.ui.navigation.AppNavigation
import com.fftri.triathlon.ui.theme.TriathlonTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            TriathlonTheme {
                AppNavigation()
            }
        }
    }
}
