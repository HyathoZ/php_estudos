<?php
class TMDB {
    private $apiKey = 'f2fbd21b14ebbd5a6594f797ddff2613'; // Sua chave de API
    private $baseUrl = 'https://api.themoviedb.org/3';
    private $bearerToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmMmZiZDIxYjE0ZWJiZDVhNjU5NGY3OTdkZGZmMjYxMyIsIm5iZiI6MTc0NjA2MDkxMi43MDUsInN1YiI6IjY4MTJjNjcwZGQ5OWRhODY3YzAyMGJiZSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.YRWAsG-DBLrar1ZtCsIa9kO3QdCRcfXSDAiAgKZNz1k';

    public function getPopularMovies() {
        $endpoint = $this->baseUrl . '/movie/popular?language=pt-BR';
        return $this->makeRequest($endpoint);
    }

    public function searchMovie($query) {
        $endpoint = $this->baseUrl . '/search/movie?language=pt-BR&query=' . urlencode($query);
        return $this->makeRequest($endpoint);
    }

    public function getMovieDetails($movieId) {
        $endpoint = $this->baseUrl . "/movie/{$movieId}?language=pt-BR";
        return $this->makeRequest($endpoint);
    }

    public function getMovieVideos($movieId) {
        $endpoint = $this->baseUrl . "/movie/{$movieId}/videos?language=pt-BR";
        return $this->makeRequest($endpoint);
    }

    private function makeRequest($url) {
        $headers = [
            "Authorization: Bearer " . $this->bearerToken,
            "Content-Type: application/json;charset=utf-8"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
?>