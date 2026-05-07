package com.fftri.triathlon.data.preferences

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.*
import androidx.datastore.preferences.preferencesDataStore
import com.fftri.triathlon.data.api.ApiClient
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "session")

class SessionManager(private val context: Context) {

    companion object {
        private val KEY_TOKEN    = stringPreferencesKey("token")
        private val KEY_USER_ID  = intPreferencesKey("user_id")
        private val KEY_NAME     = stringPreferencesKey("user_name")
        private val KEY_EMAIL    = stringPreferencesKey("user_email")
        private val KEY_ROLE     = stringPreferencesKey("user_role")
        private val KEY_CLUB_ID  = intPreferencesKey("club_id")
    }

    suspend fun saveSession(token: String, userId: Int, name: String, email: String, role: String, clubId: Int?) {
        context.dataStore.edit { prefs ->
            prefs[KEY_TOKEN]   = token
            prefs[KEY_USER_ID] = userId
            prefs[KEY_NAME]    = name
            prefs[KEY_EMAIL]   = email
            prefs[KEY_ROLE]    = role
            if (clubId != null) prefs[KEY_CLUB_ID] = clubId else prefs.remove(KEY_CLUB_ID)
        }
        ApiClient.setToken(token)
    }

    suspend fun clearSession() {
        context.dataStore.edit { it.clear() }
        ApiClient.clearToken()
    }

    val token: Flow<String?> = context.dataStore.data.map { it[KEY_TOKEN] }
    val userId: Flow<Int?>   = context.dataStore.data.map { it[KEY_USER_ID] }
    val userName: Flow<String?> = context.dataStore.data.map { it[KEY_NAME] }
    val userRole: Flow<String?> = context.dataStore.data.map { it[KEY_ROLE] }
    val clubId: Flow<Int?>   = context.dataStore.data.map { it[KEY_CLUB_ID] }

    fun isLoggedIn(): Flow<Boolean> = token.map { !it.isNullOrBlank() }
}
