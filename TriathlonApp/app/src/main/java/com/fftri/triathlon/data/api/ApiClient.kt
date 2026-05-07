package com.fftri.triathlon.data.api

import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object ApiClient {

    // Émulateur Android → 10.0.2.2 pointe vers localhost de la machine hôte
    // Appareil réel sur même réseau → remplacer par l'IP de la machine (ex: 192.168.1.X)
    private const val BASE_URL = "http://10.0.2.2/triathlon-api/public/api/"

    private var token: String? = null

    fun setToken(t: String?) { token = t }
    fun clearToken() { token = null }

    private val loggingInterceptor = HttpLoggingInterceptor().apply {
        level = HttpLoggingInterceptor.Level.BODY
    }

    private val httpClient: OkHttpClient
        get() = OkHttpClient.Builder()
            .connectTimeout(15, TimeUnit.SECONDS)
            .readTimeout(15, TimeUnit.SECONDS)
            .addInterceptor { chain ->
                val request = chain.request().newBuilder().apply {
                    token?.let { addHeader("Authorization", "Bearer $it") }
                    addHeader("Accept", "application/json")
                    addHeader("Content-Type", "application/json")
                }.build()
                chain.proceed(request)
            }
            .addInterceptor(loggingInterceptor)
            .build()

    val service: ApiService
        get() = Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(httpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
}
