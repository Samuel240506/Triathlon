package com.fftri.triathlon.ui.navigation

import androidx.compose.runtime.*
import androidx.compose.ui.platform.LocalContext
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.navigation.NavType
import androidx.navigation.compose.*
import androidx.navigation.navArgument
import com.fftri.triathlon.TriathlonApplication
import com.fftri.triathlon.data.api.ApiClient
import com.fftri.triathlon.ui.auth.LoginScreen
import com.fftri.triathlon.ui.club.*
import com.fftri.triathlon.ui.registrations.*
import com.fftri.triathlon.ui.triathlons.*
import kotlinx.coroutines.launch

object Routes {
    const val LOGIN           = "login"
    const val TRIATHLONS      = "triathlons"
    const val TRIATHLON_DETAIL= "triathlon/{id}"
    const val MY_REGISTRATIONS= "registrations/{licencieId}"
    const val CLUB_DASHBOARD  = "club/{clubId}"
    const val ATHLETES        = "athletes/{clubId}"
    const val ATHLETE_DETAIL  = "athlete/{id}"
    const val CLUB_REGISTRATIONS = "club-registrations"

    fun triathlonDetail(id: Int) = "triathlon/$id"
    fun myRegistrations(licencieId: Int) = "registrations/$licencieId"
    fun clubDashboard(clubId: Int) = "club/$clubId"
    fun athletes(clubId: Int) = "athletes/$clubId"
    fun athleteDetail(id: Int) = "athlete/$id"
}

@Composable
fun AppNavigation() {
    val context     = LocalContext.current
    val app         = context.applicationContext as TriathlonApplication
    val session     = app.sessionManager
    val scope       = rememberCoroutineScope()
    val navController = rememberNavController()

    val token   by session.token.collectAsStateWithLifecycle(null)
    val role    by session.userRole.collectAsStateWithLifecycle(null)
    val clubId  by session.clubId.collectAsStateWithLifecycle(null)

    // Restore token on relaunch
    LaunchedEffect(token) {
        if (!token.isNullOrBlank()) ApiClient.setToken(token)
    }

    val startDest = Routes.LOGIN

    NavHost(navController = navController, startDestination = startDest) {

        composable(Routes.LOGIN) {
            LoginScreen(onLoginSuccess = {
                val dest = if (role == "club_manager" && clubId != null)
                    Routes.clubDashboard(clubId!!)
                else
                    Routes.TRIATHLONS
                navController.navigate(dest) {
                    popUpTo(Routes.LOGIN) { inclusive = true }
                }
            })
        }

        // ── Triathlons ────────────────────────────────────────────────────────
        composable(Routes.TRIATHLONS) {
            TriathlonListScreen(
                onTriathlonClick = { navController.navigate(Routes.triathlonDetail(it)) },
                onLogout = {
                    scope.launch {
                        session.clearSession()
                        navController.navigate(Routes.LOGIN) { popUpTo(0) { inclusive = true } }
                    }
                }
            )
        }

        composable(
            Routes.TRIATHLON_DETAIL,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) { back ->
            val id = back.arguments!!.getInt("id")
            TriathlonDetailScreen(
                triathlonId = id,
                licencieId  = null, // L'inscription se gère via le dashboard club
                onBack      = { navController.popBackStack() },
                onRegistered = { navController.popBackStack() }
            )
        }

        // ── Club manager ──────────────────────────────────────────────────────
        composable(
            Routes.CLUB_DASHBOARD,
            arguments = listOf(navArgument("clubId") { type = NavType.IntType })
        ) { back ->
            val id = back.arguments!!.getInt("clubId")
            ClubDashboardScreen(
                clubId = id,
                onAthletes     = { navController.navigate(Routes.athletes(id)) },
                onRegistrations = { navController.navigate(Routes.CLUB_REGISTRATIONS) },
                onTriathlons   = { navController.navigate(Routes.TRIATHLONS) },
                onLogout = {
                    scope.launch {
                        session.clearSession()
                        navController.navigate(Routes.LOGIN) { popUpTo(0) { inclusive = true } }
                    }
                }
            )
        }

        composable(
            Routes.ATHLETES,
            arguments = listOf(navArgument("clubId") { type = NavType.IntType })
        ) { back ->
            val id = back.arguments!!.getInt("clubId")
            LicencieListScreen(
                clubId = id,
                onBack = { navController.popBackStack() },
                onLicencieClick = { navController.navigate(Routes.athleteDetail(it)) }
            )
        }

        composable(
            Routes.ATHLETE_DETAIL,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) { back ->
            LicencieDetailScreen(
                licencieId = back.arguments!!.getInt("id"),
                onBack = { navController.popBackStack() }
            )
        }

        composable(Routes.CLUB_REGISTRATIONS) {
            ClubRegistrationsScreen(onBack = { navController.popBackStack() })
        }

        composable(
            Routes.MY_REGISTRATIONS,
            arguments = listOf(navArgument("licencieId") { type = NavType.IntType })
        ) { back ->
            MyRegistrationsScreen(
                licencieId = back.arguments!!.getInt("licencieId"),
                onBack = { navController.popBackStack() }
            )
        }
    }
}
