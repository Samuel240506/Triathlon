package com.fftri.triathlon.data.api

import com.fftri.triathlon.data.api.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // Auth
    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @GET("me")
    suspend fun me(): Response<UserDto>

    // Clubs
    @GET("clubs")
    suspend fun getClubs(): Response<List<ClubDto>>

    @GET("clubs/{id}")
    suspend fun getClub(@Path("id") id: Int): Response<ClubDto>

    // Triathlons
    @GET("triathlons")
    suspend fun getTriathlons(@Query("upcoming") upcoming: Boolean? = null): Response<List<TriathlonDto>>

    @GET("triathlons/{id}")
    suspend fun getTriathlon(@Path("id") id: Int): Response<TriathlonDto>

    @GET("triathlons/types")
    suspend fun getTypes(): Response<List<TypeTriathlonDto>>

    // Licencies
    @GET("licencies")
    suspend fun getLicencies(
        @Query("club_id") clubId: Int? = null,
        @Query("q") search: String? = null
    ): Response<List<LicencieDto>>

    @GET("licencies/{id}")
    suspend fun getLicencie(@Path("id") id: Int): Response<LicencieDto>

    // Registrations
    @GET("registrations")
    suspend fun getRegistrations(
        @Query("triathlon_id") triathlonId: Int? = null,
        @Query("licencie_id") licencieId: Int? = null,
        @Query("status") status: String? = null
    ): Response<List<RegistrationDto>>

    @POST("registrations")
    suspend fun createRegistration(@Body request: CreateRegistrationRequest): Response<RegistrationDto>

    @PATCH("registrations/{id}/status")
    suspend fun updateStatus(
        @Path("id") id: Int,
        @Body request: UpdateStatusRequest
    ): Response<RegistrationDto>

    @DELETE("registrations/{id}")
    suspend fun deleteRegistration(@Path("id") id: Int): Response<Unit>
}
