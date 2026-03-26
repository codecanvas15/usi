  @php
      function display_coa($coa, $depth = 1)
      {
          foreach ($coa->childs ?? $coa as $key => $child) {
              echo '<li>';
              if (count($child->childs) > 0) {
                  echo '<span><i class="fa fa-folder-open"></i> &nbsp;' . Str::headline($child->account_code) . ' - ' . Str::headline($child->name) . '</span>';
                  echo '<ul>';
                  display_coa($child);
                  echo '</ul>';
              } else {
                  echo '<span><i class="fa fa-minus-square"></i>  &nbsp;' . Str::headline($child->account_code) . ' - ' . Str::headline($child->name) . '</span>';
              }
              echo '</li>';
          }
      }
  @endphp
  <div>
      <div class="tree-coa mt-10" id="coa">
          <ul>
              @foreach ($results as $key => $item)
                  <li>
                      @if (count($item->childs) > 0)
                          <span><i class="fa fa-folder-open"></i> &nbsp;{{ Str::headline($item->account_code) }} - {{ Str::headline($item->name) }}</span>
                          <ul>
                              @php
                                  display_coa($item);
                              @endphp
                          </ul>
                      @else
                          <span><i class="fa fa-minus-square"></i> &nbsp;{{ Str::headline($item->account_code) }} - {{ Str::headline($item->name) }}</span>
                      @endif
                  </li>
              @endforeach
          </ul>
      </div>
  </div>
  {{-- @foreach ($results as $key => $item)
      <div>
          <div class="tree-coa mt-10" id="coa-{{ $key }}">
              <ul>
                  <li>
                      <span><i class="fa fa-folder-open"></i>{{ Str::headline($key) }}</span>
                      @foreach ($item as $item_data)
                          <ul class="border-left" style="border-color: #999">
                              <li>
                                  <span><i class="fa fa-folder-open"></i>{{ "$item_data->account_code - $item_data->name" }}</span>
                                  <ul class="border-left" style="border-color: #999">
                                      @foreach ($item_data->childs as $child)
                                          @if ($child->childs->count() > 0)
                                              <li> <span><i class="fa fa-minus-square"></i> {{ "$child->account_code - $child->name" }} </span>
                                                  @foreach ($child->childs as $item_child)
                                                      <ul>
                                                          <li> <span> {{ "$item_child->account_code - $item_child->name" }}</span></li>
                                                      </ul>
                                                  @endforeach
                                              </li>
                                          @else
                                              <li>
                                                  <span>{{ "$child->account_code - $child->name" }}</span>
                                              </li>
                                          @endif
                                      @endforeach
                                  </ul>
                              </li>
                          </ul>
                      @endforeach
                  </li>
              </ul>
          </div>
      </div>
  @endforeach --}}
