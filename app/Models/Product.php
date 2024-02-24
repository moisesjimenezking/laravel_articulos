<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\Handler\NotFound;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\Paginator;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author', 
        'title', 
        'description', 
        'urlToImage', 
        'publishedAt'
    ];

    public static function externalApi(){
        $client = new Client();
        $query = "nike";
        $url = "https://newsapi.org/v2/everything?q=".$query.
            "&from=".date('Y-m-d', strtotime(date("Y-m-d")."- 1 day")).
            "&sortBy=publishedAt&apiKey=193a943f8a444e788949c8b373b0c7f6";

        $response = $client->get($url);
        $productsData = json_decode($response->getBody(), true);
        $response = collect();
        
        if($productsData["status"] == "ok" && count($productsData["articles"]) > 0){
            $page = $productsData['totalResults'] > 0 ? round($productsData['totalResults']/10, PHP_ROUND_HALF_UP) : 0;
            $response = self::serialize($productsData["articles"]);
        }
    
        // Paginar los resultados
        $perPage = 10; // Número de elementos por página
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentItems = $response->forPage($currentPage, $perPage);
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $response->count(), $perPage, $currentPage);
    
        return $paginatedItems;
    }
    
    public static function serialize($data){
        $products = collect($data)->map(function ($data) {
            $client = new Client();
            $author = $client->get("https://randomuser.me/api/");
            $author = json_decode($author->getBody(), true);
            $author = $author["results"][0]["name"]["title"].". ".$author["results"][0]["name"]["first"]." ".$author["results"][0]["name"]["last"];

            return new Product([
                'name'        => $data['source']['name'],
                'author'      => $author,
                'title'       => $data['title'],
                'description' => $data['description'],
                'urlToImage'  => $data['urlToImage'],
                'publishedAt' => $data['publishedAt'],
            ]);
        });

        return $products;
    }
}
