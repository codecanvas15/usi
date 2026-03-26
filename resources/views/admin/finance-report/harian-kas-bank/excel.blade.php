  @php
      $row_start = 7;
  @endphp
  <table>
      <tr>
          <td>
              <p><b>{{ getCompany()->name }}</b></p>
              <p><b>{{ getCompany()->address }}</b></p>
              <p><b>Telp. {{ getCompany()->phone }}</b></p>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>
              {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
          </td>
      </tr>
      <tr>
          <td></td>
      </tr>
      <tr>
          <td colspan="6" align="center">
              <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
          </td>
      </tr>
      <tr>
          <td colspan="6" align="center">
              <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
          </td>
      </tr>
      @if ($coa)
          @php
              $row_start++;
          @endphp
          <tr>
              <td colspan="6" align="center">
                  <p><b>KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</b></p>
              </td>
          </tr>
      @endif
  </table>

  <table>
      <thead>
          <tr>
              <th align="center" rowspan="2"><b>NO.</b></th>
              <th align="center" rowspan="2"><b>BANK</b></th>
              <th align="center" rowspan="2"><b>SALDO AWAL</b></th>
              <th align="center" colspan="2"><b>MUTASI</b></th>
              <th align="center" rowspan="2"><b>SALDO AKHIR</b></th>
          </tr>
          <tr>
              <th align="center"><b>PENERIMAAN</b></th>
              <th align="center"><b>PENGELUARAN</b></th>
          </tr>
      </thead>
      <tbody>
          @forelse ($data as $key => $d)
              <tr>
                  <td align="center">{{ $key + 1 }}.</td>
                  <td>{{ $d->account_code }} - {{ $d->name }}</td>
                  <td align="right">{{ $d->balance_amount_before }}</td>
                  <td align="right">{{ $d->mutation_debit }}</td>
                  <td align="right">{{ $d->mutation_credit }}</td>
                  <td align="right">{{ $d->balance_final }}</td>
              </tr>
          @empty
              <tr>
                  <td align="center" colspan="6">
                      Tidak ada data
                  </td>
              </tr>
          @endforelse
      </tbody>
      <tfoot>
          <tr>
              <th align="center"></th>
              <th><b>TOTAL</b></th>
              <th align="right"><b></b></th>
              <th align="right"><b>=SUM(D{{ $row_start }}:D{{ $row_start + count($data) }})</b></th>
              <th align="right"><b>=SUM(E{{ $row_start }}:E{{ $row_start + count($data) }})</b></th>
              <th align="right"><b></b></th>
          </tr>
      </tfoot>
  </table>
