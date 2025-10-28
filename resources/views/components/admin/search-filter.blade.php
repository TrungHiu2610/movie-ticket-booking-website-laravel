@props([
'searchPlaceholder' => 'Tìm kiếm...',
'filters' => [], // Array of filters: [['name' => 'status', 'label' => 'Trạng thái', 'options' => ['active' => 'Hoạt động']]]
'sortOptions' => [], // Array: ['title' => 'Tiêu đề', 'created_at' => 'Ngày tạo']
'showReset' => true,
])

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ request()->url() }}" class="row g-3">
            <!-- Search Input -->
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="{{ $searchPlaceholder }}"
                    value="{{ request('search') }}">
            </div>

            <!-- Dynamic Filters -->
            @foreach($filters as $filter)
            <div class="col-md-{{ $filter['col'] ?? 3 }}">
                <label class="form-label">{{ $filter['label'] }}</label>
                @if(count($filter['options']) === 0)
                <!-- Date input for filters with no options -->
                <input type="date"
                    name="{{ $filter['name'] }}"
                    class="form-control"
                    value="{{ request($filter['name']) }}">
                @else
                <!-- Select dropdown for filters with options -->
                <select name="{{ $filter['name'] }}" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($filter['options'] as $value => $label)
                    <option value="{{ $value }}"
                        {{ request($filter['name']) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @endif
            </div>
            @endforeach

            <!-- Sort Dropdown -->
            @if(count($sortOptions) > 0)
            <div class="col-md-2">
                <label class="form-label">Sắp xếp theo</label>
                <select name="sort" class="form-select">
                    @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}"
                        {{ request('sort') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Thứ tự</label>
                <select name="direction" class="form-select">
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                </select>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                @if($showReset)
                <a href="{{ request()->url() }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
                @endif
            </div>
        </form>
    </div>
</div>