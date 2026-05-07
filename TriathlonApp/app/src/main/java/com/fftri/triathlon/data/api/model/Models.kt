package com.fftri.triathlon.data.api.model

import com.google.gson.annotations.SerializedName

// ── Auth ──────────────────────────────────────────────────────────────────────

data class LoginRequest(val email: String, val password: String)

data class LoginResponse(
    val token: String,
    val user: UserDto
)

data class UserDto(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    @SerializedName("club_id") val clubId: Int?
)

// ── Club ──────────────────────────────────────────────────────────────────────

data class ClubDto(
    val id: Int,
    val name: String,
    val address: String?,
    val city: String,
    val phone: String?,
    val email: String?
)

// ── Licencie ──────────────────────────────────────────────────────────────────

data class LicencieDto(
    val id: Int,
    @SerializedName("license_number") val licenseNumber: String,
    @SerializedName("first_name") val firstName: String,
    @SerializedName("last_name") val lastName: String,
    @SerializedName("birth_date") val birthDate: String?,
    val gender: String,
    val category: String,
    @SerializedName("license_type") val licenseType: String,
    @SerializedName("club_id") val clubId: Int,
    val email: String?,
    val phone: String?
) {
    val fullName: String get() = "$firstName $lastName"
}

// ── Triathlon ─────────────────────────────────────────────────────────────────

data class TriathlonDto(
    val id: Int,
    val name: String,
    val type: String,
    val location: String,
    @SerializedName("event_date") val eventDate: String,
    val description: String?,
    @SerializedName("max_participants") val maxParticipants: Int?,
    @SerializedName("registration_deadline") val registrationDeadline: String?
)

data class TypeTriathlonDto(
    val codeType: String,
    val libelle: String
)

// ── Registration ──────────────────────────────────────────────────────────────

data class RegistrationDto(
    val id: Int,
    val status: String,
    @SerializedName("registration_date") val registrationDate: String,
    val triathlon: TriathlonSummaryDto?,
    val licencie: LicencieSummaryDto?
)

data class TriathlonSummaryDto(
    val id: Int,
    val name: String,
    val type: String,
    val location: String,
    @SerializedName("event_date") val eventDate: String
)

data class LicencieSummaryDto(
    val id: Int,
    @SerializedName("license_number") val licenseNumber: String,
    @SerializedName("first_name") val firstName: String,
    @SerializedName("last_name") val lastName: String
) {
    val fullName: String get() = "$firstName $lastName"
}

data class CreateRegistrationRequest(
    @SerializedName("triathlon_id") val triathlonId: Int,
    @SerializedName("licencie_id") val licencieId: Int
)

data class UpdateStatusRequest(val status: String)
