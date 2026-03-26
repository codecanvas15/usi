<div class="row">
    <table>
        <tr>
            <td>
                <h2 class="text-danger text-uppercase my-0">{{ Str::upper(getCompany()->name) }}</h2>
                <p class="small-font my-0">{{ getCompany()->address }}</p>
                <p class="small-font my-0">Telp. {{ getCompany()->phone }}</p>
                @if (getCompany()->npwp)
                    <p class="small-font my-0">NPWP. {{ getCompany()->npwp }}</p>
                @endif
            </td>
            <td style="width: 25%" class="text-right valign-top">
                <img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" style="width: 100px">
            </td>
        </tr>
    </table>
</div>
