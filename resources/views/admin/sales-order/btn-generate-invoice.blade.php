<x-button color="info" label="generate invoice" target="blank" size="sm" icon="plus" fontawesome soft :link="route('admin.invoice-trading.generate', ['id' => $row->id])" />
