<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ExtractService
{
    protected $productSKU;

    public function __construct()
    {
        $this->productSKU = Cache::remember('product_sku', 60 * 60 * 24, function () {
            return \App\Models\Product::all()->pluck('sku');
        });
    }

    public function extractSPX($document)
    {
        $documents = explode("\n", $document);

        $startProductIndex = null;
        $endProductIndex = null;
        $awb_number = '';
        for ($i = 0; $i < count($documents); $i++) {
            $words = str_replace(' ', '', $documents[$i]);
            if (str_contains($words, 'No.Resi:')) {
                $awb_number = explode(' ', $documents[$i])[2];
            }

            if (str_contains($words, '#NamaProduk')) {
                $startProductIndex = $i + 1;
            }

            if (str_contains($words, 'Pesan:')) {
                $endProductIndex = $i;
            }
        }

        $productDatas = [];
        $product = '';
        for ($i = ($startProductIndex); $i < $endProductIndex; $i++) {
            if (is_numeric($documents[$i][0]) && $product != '') {
                $productDatas[] = $product;
                $product = '';
            }

            $product .= ($product != '' ? '|' : '') . $documents[$i];

            if ($i == $endProductIndex - 1) {
                if (is_numeric($product)) {
                    $productDatas[count($productDatas) - 1] .= $product;
                } else {
                    $productDatas[] = $product;
                }
            }
        }

        return $productDatas;

        $products = [];
        foreach ($productDatas as $data) {
            $qty = '1';
            if (is_numeric(substr($data, -1))) {
                $qty = substr($data, -1);
            }

            $descs = explode('|', $data);
            // $sku = $descs[count($descs) - 1];
            // $sku = explode(' ', $sku)[0];
            $sku = null;
            for ($i = 2; $i < count($descs); $i++) {
                $sku = explode(' ', $descs[$i])[0];
                $sku = preg_replace('/[^A-Za-z0-9\-]/', ' ', $sku);
                if (explode(' ', $sku)) {
                    $sku = explode(' ', $sku)[0];
                }
                if (in_array($sku, $this->productSKU->toArray())) {
                    break;
                }
            }

            $name = substr($data, 1, -1);
            $skuIndex = strpos($name, $sku);
            if ($skuIndex !== false) {
                $name = substr($name, 0, $skuIndex - 1);
            }
            $name = str_replace('|', ' ', $name);

            $products[] = [
                'name' => trim($name),
                'sku' => $sku,
                'variant' => substr($descs[count($descs) - 1], $sku ? strlen($sku) + 1 : 0, -1),
                'qty' => $qty,
            ];
        }

        $shippingLabel = [
            'awb_number' => $awb_number,
            'products' => $products,
        ];

        return $shippingLabel;
    }
}
