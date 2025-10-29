<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? "طباعة" }}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Tahoma, Arial, sans-serif; color: #222; }
    .copy { width: 14.8cm; height: 21cm; border: 1px dashed #444; margin: 0 auto 24px; padding: 16px; }
    h2 { margin: 0 0 10px; }
    .meta { font-size: 13px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #888; padding: 6px; text-align: center; }
    .total { text-align: end; margin-top: 10px; font-size: 14px; }
    .signs { display: flex; justify-content: space-between; margin-top: 28px; font-size: 13px; }
    @media print { .no-print { display: none; } body { margin: 0; } }
  </style>
</head>
<body>
  <div class="no-print" style="text-align:center;margin:10px 0;">
    <button onclick="window.print()">🖨️ طباعة</button>
  </div>

  @foreach(['نسخة المندوب', 'نسخة الشركة', 'نسخة الصيدلية'] as $copy)
    <div class="copy">
      <h2>📦 {{ $copy }}</h2>
      <div class="meta">
        <div>رقم الطلب: <b>#{{ $order->id }}</b></div>
        <div>الصيدلية: <b>{{ $order->pharmacy->name ?? '—' }}</b></div>
        <div>الشركة: <b>{{ $order->company->name ?? '—' }}</b></div>
        <div>التاريخ: <b>{{ $order->created_at?->format('Y-m-d H:i') }}</b></div>
        <div>الحالة: <b>{{ __("statuses.$order->status") ?? $order->status }}</b></div>
      </div>

      <table>
        <thead>
          <tr>
            <th>الدواء</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الإجمالي</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
            @php
              $line = $item->line_total ?? ($item->quantity * $item->unit_price);
            @endphp
            <tr>
              <td>{{ $item->drug->name }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ number_format($item->unit_price,2) }}</td>
              <td>{{ number_format($line,2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="total">
        الإجمالي الكلي: <b>{{ number_format($order->total_amount, 2) }} د.ل</b>
      </div>

      <div class="signs">
        <div>ختم/توقيع الصيدلية: ____________</div>
        <div>ختم/توقيع المندوب: ____________</div>
      </div>
    </div>
  @endforeach
</body>
</html>
