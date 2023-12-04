<?php

// Define a class to represent a product
class Product
{
  public $name;
  public $price;

  // Constructor to initialize the product properties
  public function __construct($name, $price)
  {
    $this->name = $name;
    $this->price = $price;
  }
}

// Initialize an array to store URLs
$queue = array();

// Initialize an array to store scraped products
$products = array();

// Function to add a new URL to the queue
function addUrl(&$queue, $newUrl)
{
  array_push($queue, $newUrl);
}

// Function to scrape product information from a given URL
function scrapePage($url)
{
  // Fetch the HTML content from the URL
  $htmlContent = file_get_contents($url);

  // Check if the HTTP request was successful
  if ($htmlContent === FALSE) {
    echo "Error fetching the content from $url.\n";
    return;
  }

  // Create a new DOMDocument to parse the HTML
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML($htmlContent);
  libxml_clear_errors();

  // Initialize variables to store product details
  $title = "";
  $price = "";

  // Get all div elements in the HTML
  $divs = $dom->getElementsByTagName('div');

  // Loop through each div element
  foreach ($divs as $div) {
    // Trim leading and trailing whitespaces from the node value
    $nodeValue = trim($div->nodeValue);

    // Check if the div has the class 'product-detail-title' and the node value is not empty
    if ($div->hasAttribute('class') && $div->getAttribute('class') === 'product-detail-title' && $nodeValue !== '') {
      $title = $nodeValue;
      echo "Title: {$title}\n";
    }
    // Check if the div has the class 'product-detail-price' and the node value is not empty
    else if ($div->hasAttribute('class') && $div->getAttribute('class') === 'product-detail-price' && $nodeValue !== '') {
      $price = $nodeValue;
      echo "Price: {$price}\n";
    }
  }

  // Create a new Product object with the scraped details
  $product = new Product($title, $price);

  // Return the Product object
  return $product;
}

// Function to scrape product information from all URLs in the queue
function scrapeUrls(&$queue)
{
  global $products;

  // Use a while loop to continuously process URLs in the queue
  while (!empty($queue)) {
    // Dequeue a URL from the front of the queue
    $url = array_shift($queue);

    // Scrape product information from the URL and add it to the products array
    $product = scrapePage($url);
    array_push($products, $product);
  }
}

// Function to search for a product based on a keyword
function search($keyword)
{
  global $products;
  $found = false;

  // Loop through each product in the products array
  foreach ($products as $product) {
    // Check if the keyword is present in the product name (case-insensitive)
    if (stristr($product->name, $keyword)) {
      echo "The searched product is: {$product->name}\n";
      $found = true;
    }
  }

  // Display a message if the keyword is not found in any product names
  if (!$found) {
    echo "Results not found.\n";
  }
}

// Main Code

// Add URLs to the queue
addUrl($queue, "https://www.telemart.pk/infinix-hot-30-play-4gb-64gb-dual-sim-with-official-warranty");
addUrl($queue, "https://www.telemart.pk/infinix-zero-30-4g-8gb-256gb-dual-sim-with-official-warranty");
addUrl($queue, "https://www.telemart.pk/tecno-pova-5-pro-8gb-256gb-dual-sim-with-official-warranty");
addUrl($queue, "https://www.telemart.pk/tecno-phantom-v-fold-5g-12gb-512gb-with-official-warranty");

// Scrape product information from the URLs in the queue
scrapeUrls($queue);
