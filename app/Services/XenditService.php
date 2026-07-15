<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceItem;

class XenditService
{
    private InvoiceApi $invoiceApi;

    public function __construct()
    {
        Configuration::setXenditKey(config('xendit.secret_key'));
        $this->invoiceApi = new InvoiceApi();
    }

    /**
     * Buat invoice Xendit dan return invoice data
     */
    public function createInvoice(array $params): array
    {
        $items = [];
        if (!empty($params['items'])) {
            foreach ($params['items'] as $item) {
                $items[] = new InvoiceItem([
                    'name'     => $item['name'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                    'category' => $item['category'] ?? 'Flight Ticket',
                ]);
            }
        }

        $createInvoiceRequest = new CreateInvoiceRequest([
            'external_id'          => $params['external_id'],
            'amount'               => (int) $params['amount'],
            'payer_email'          => $params['email'],
            'description'          => $params['description'],
            'success_redirect_url' => $params['success_url'],
            'failure_redirect_url' => $params['failure_url'],
            'currency'             => 'IDR',
            'items'                => $items,
            'customer'             => [
                'given_names' => $params['customer_name'],
                'email'       => $params['email'],
                'mobile_number' => $params['phone'] ?? null,
            ],
        ]);

        $response = $this->invoiceApi->createInvoice($createInvoiceRequest);

        return [
            'invoice_id'  => $response->getId(),
            'invoice_url' => $response->getInvoiceUrl(),
            'status'      => $response->getStatus(),
            'amount'      => $response->getAmount(),
        ];
    }

    /**
     * Ambil invoice by ID
     */
    public function getInvoice(string $invoiceId): array
    {
        $invoice = $this->invoiceApi->getInvoiceById($invoiceId);

        return [
            'invoice_id' => $invoice->getId(),
            'status'     => $invoice->getStatus(), // PENDING, PAID, EXPIRED, etc.
            'amount'     => $invoice->getAmount(),
            'external_id' => $invoice->getExternalId(),
        ];
    }
}
