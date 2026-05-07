package com.fftri.triathlon

import android.app.Application
import com.fftri.triathlon.data.preferences.SessionManager

class TriathlonApplication : Application() {
    lateinit var sessionManager: SessionManager
        private set

    override fun onCreate() {
        super.onCreate()
        sessionManager = SessionManager(this)
    }
}
