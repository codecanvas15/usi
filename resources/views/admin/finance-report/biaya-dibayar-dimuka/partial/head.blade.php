<tr class="{{ isset($ext) && $ext == 'pdf' ? 'text-white' : '' }}">
    <th class="font-small-1" rowspan="2" @if ($formatNumber) width="5%" @endif style="vertical-align: middle; text-align: center">No COA</th>
    <th class="font-small-1" rowspan="2" @if ($formatNumber) width="20%" @endif style="vertical-align: middle; text-align: center">NAMA BARANG</th>
    <th class="font-small-1" colspan="2" style="vertical-align: middle; text-align: center">PEROLEHAN</th>
    <th class="font-small-1" @if ($formatNumber) width="3%" @endif style="vertical-align: middle; text-align: center">UMUR</th>
    <th class="font-small-1" rowspan="2" @if ($formatNumber) width="10%" @endif style="vertical-align: middle; text-align: center">PERIOD</th>
    <th class="font-small-1" rowspan="2" @if ($formatNumber) width="7%" @endif style="vertical-align: middle; text-align: center">PERBULAN</th>
    <th class="font-small-1" rowspan="2" @if ($formatNumber) width="3%" @endif style="vertical-align: middle; text-align: center">KE</th>
    <th class="font-small-1" colspan="3" style="vertical-align: middle; text-align: center">AMORTISASI</th>
    <th class="font-small-1" rowspan="2" style="vertical-align: middle; text-align: center">KETERANGAN</th>
</tr>
<tr class="{{ isset($ext) && $ext == 'pdf' ? 'text-white' : '' }}">
    <th class="font-small-1" @if ($formatNumber) width="5%" @endif style="vertical-align: middle; text-align: center">TANGGAL</th>
    <th class="font-small-1" @if ($formatNumber) width="7%" @endif style="vertical-align: middle; text-align: center">NOMINAL</th>
    <th class="font-small-1" style="vertical-align: middle; text-align: center">(bulan)</th>
    <th class="font-small-1" @if ($formatNumber) width="7%" @endif style="vertical-align: middle; text-align: center">BLN INI</th>
    <th class="font-small-1" @if ($formatNumber) width="7%" @endif style="vertical-align: middle; text-align: center">AKUMULASI</th>
    <th class="font-small-1" @if ($formatNumber) width="7% @endif style="vertical-align: middle; text-align: center">NILAI BUKU</th>
</tr>
