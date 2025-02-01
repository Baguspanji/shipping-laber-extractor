<?php

namespace App\Http\Controllers;

use App\Models\OrderDelivery;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $datas = OrderDelivery::all();
        return view('home', compact('datas'));
    }

    // upload file
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        $image = $request->file('file');
        $image_uploaded = $image->store('upload');

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile(storage_path('app/public/' . $image_uploaded));

        // or extract the text of a specific page (in this case the first page)
        // $document = $pdf->getPages()[2]->getText();
        // $document = explode("\n", $document);
        // $document = $this->extractDetail($document);

        $documents = [];
        for ($i = 0; $i < count($pdf->getPages()); $i++) {
            $document = $pdf->getPages()[$i]->getText();
            $document = $this->extractDetail($document);

            $documents[] = $document;
        }

        $checkExistAwb = OrderDelivery::whereIn('awb_number', array_column($documents, 'awb_number'))->get();
        $insertDocuments = [];
        foreach ($documents as $key => $document) {
            if ($checkExistAwb->contains('awb_number', $document['awb_number'])) {
                break;
            }
            $insertDocuments[] = OrderDelivery::insert($document);
        }

        if (count($insertDocuments) == 0) {
            return redirect()->back()->with('error', 'Awb Number already exist');
        }

        return redirect()->back()->with('success', 'Awb Number successfully uploaded');
    }

    private function extractDetail($document)
    {
        $document = explode("\n", $document);

        $indexCod = strpos($document[2], 'COD') !== false ? 2 : 1;
        $shippingPhone = $document[$indexCod + 2];
        $receiverName = $document[$indexCod + 3];

        // find index "Berat:"
        $indexBerat = 0;
        // find index "#Nama Produk\tSKU Variasi Qty":"
        $indexProduct = 0;
        // find index "Pesan:"
        $indexPesan = 0;
        for ($i = 0; $i < count($document); $i++) {
            if (str_contains($document[$i], 'Berat:')) {
                $indexBerat = $i;
            }

            if (str_contains($document[$i], 'Nama Produk')) {
                $indexProduct = $i;
            }

            if (str_contains($document[$i], 'Pesan:')) {
                $indexPesan = $i;
            }
        }

        $receiverAddress = '';
        for ($i = 4; $i < 8; $i++) {
            if (($indexCod + $i) == $indexBerat) break;
            $receiverAddress .= $document[$indexCod + $i] . ' ';
        }

        $shippingName = $document[$indexBerat + 2];
        $deliveryDate = $document[$indexBerat + 3];
        $weight = $document[$indexBerat + 4];

        $productDatas = [];
        $productDescs = [];
        $product = '';
        $productDesc = '';
        for ($i = ($indexProduct + 1); $i < $indexPesan; $i++) {
            if (is_numeric($document[$i][0]) && $product != '') {
                // reduce same word in product
                $product = substr($product, 1, -strlen($productDesc) - 2);

                $productDatas[] = $product;
                $productDescs[] = $productDesc;
                $product = '';
            }

            $product .= $document[$i] . ' ';
            if ($i == $indexPesan - 1) {
                // length of productDesc
                $product = substr($product, 1, -strlen($document[$i]) - 2);

                $productDatas[] = $product;
                $productDescs[] = $document[$i];
            }

            $productDesc = $document[$i];
        }

        $products = [];
        foreach ($productDatas as $key => $data) {
            $desc = $productDescs[$key];
            $sku = explode(' ', $desc)[0];

            $products[] = [
                'name' => $data,
                'sku' => $sku,
                'variant' => substr($desc, strlen($sku) + 1, -1),
                'qty' => substr($desc, -1),
            ];
        }

        $receiverAddresses = $document[$indexPesan + 4];
        $receiverAddresses = str_replace("\t", ' ', $receiverAddresses);
        $receiverAddresses = explode(' ', $receiverAddresses);

        $shippingCoder = $document[$indexPesan + 1];
        $shippingCode = explode('-', $shippingCoder);
        $shippingCode = $shippingCode[0] . '-' . $shippingCode[1] . '-' . substr($shippingCode[2], 0, 2);

        $shippingLabel = [
            'awb_number' => explode(' ', $document[0])[2],
            'shipping_address' => $document[1],
            'cod' => $indexCod == 2 ? true : false,
            // 'receiver_name' => $document[3],
            'shipping_phone' => $shippingPhone,
            'receiver_name' => $receiverName,
            'receiver_address' => $receiverAddress,
            'shipping_name' => $shippingName,
            'delivery_date' => $deliveryDate,
            'weight' => $weight,
            'products' => json_encode($products),
            'shipping_code1' => $shippingCode,
            'shipping_code2' => substr($shippingCoder, strlen($shippingCode)),
            'shipping_service' => $document[$indexPesan + 2],
            'receiver_place' => $document[$indexPesan + 3],
            'receiver_city' =>  $receiverAddresses[0] . ' ' . $receiverAddresses[1],
            'receiver_district' => $receiverAddresses[2],
            'receiver_village' => $receiverAddresses[3],
            'no_reference' => explode(' ', $document[$indexPesan + 6])[1],
            'cod_check' => explode(':', $document[$indexPesan + 7])[1] == 'Ya' ? true : false,
        ];

        return $shippingLabel;
    }
}
