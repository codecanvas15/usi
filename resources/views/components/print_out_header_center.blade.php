<link rel="stylesheet" href="{{ public_path() }}/assets/icons/font-awesome/css/font-awesome.css">
<div class="row">
    <table>
        <tr>
            <td style="width: 20%" class="text-left valign-top">
                <img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" style="height: 60px">
            </td>
            <td class="text-center">
                <h1 class="text-danger text-uppercase my-0 font-medium-4">{{ Str::upper(getCompany()->name) }}</h1>
                @if (getCompany()->letter_subtitle)
                    <h3 class="font-small-3 my-0">{{ getCompany()->letter_subtitle }}</h3>
                @endif
                <div class="text-center">
                    <p class="font-small-3 my-0"><i class="fa fa-map-marker font-medium-1" style="margin-bottom:-3px"></i> <span>{{ getCompany()->address }}</span></p>
                </div>
                <div class="text-center">
                    <p class="font-small-3 my-0"><i class="fa fa-envelope font-small-3" style="margin-bottom:-3px"></i> <span>{{ getCompany()->email }} &nbsp; <i class="fa fa-globe font-small-3" style="margin-bottom:-3px"></i> <span>{{ getCompany()->website }}</span></span></p>
                </div>
            </td>
            <td style="width: 20%" class="text-right valign-top">
                @if (getCompany()->secondary_logo)
                    <img src="{{ public_path('/storage/' . getCompany()->secondary_logo) }}" style="height: 50px">
                @endif
            </td>
        </tr>
    </table>
    <hr style="border: 0.5px solid grey;" class="my-0 py-0">
</div>
