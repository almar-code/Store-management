@extends('layouts.master')
@section('link')
    <link href="assets/css/chart.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('content')
 <div class="wrapper">
        <div class="top-header" dir="rtl">

    <div class="header-top">

        <div class="left-title">
            <h2>لوحة التحكم</h2>
            <p>تابع اداء موقعك الإلكتروني</p>
        </div>

        <div class="right-info">

            <div class="info-box">
                <span class="label">تاريح البدء</span>
                <span class="value">Oct 10, 2018</span>
            </div>

            <div class="divider"></div>

            <div class="info-box">
                <span class="label">تاريخ الانتهاء</span>
                <span class="value">Oct 23, 2018</span>
            </div>

            <div class="divider"></div>

            <button class="export-btn">تصدير</button>

        </div>

    </div>

    <div class="header-bottom">

        <div class="tabs">
            <a class="tab active">اليوم</a>
            <a class="tab">الاسبوع</a>
            <a class="tab">الشهر</a>
            <a class="tab">السنة</a>
        </div>

        <div class="actions">
            <a>حفظ التقرير</a>
            <a>تصدير  PDF</a>
        </div>

    </div>

</div>

        <!-- ================= TOP SECTION ================= -->

        <div class="grid-layout">

            <div class="panel">
                <div class="heading">اداء الموقع الالكتروني</div>

                <div class="meta-row ">
                    <div>
                        <div class="meta-title">الطلبات</div>
                        <div class="meta-value" style="color: #14b8a6;">33</div>
                    </div>
                    <div>
                        <div class="meta-title">المستخدمين</div>
                        <div class="meta-value" style="color: #0984e3;">33.50%</div>
                    </div>
                    <div>
                        <div class="meta-title" >المبيعات</div>
                        <div class="meta-value" style="color: #9b92aa;">83,123</div>
                    </div>
                    <div>
                        <div class="meta-title">الارباح</div>
                        <div class="meta-value" style="color: #6417df;">16,869</div>
                    </div>
                </div>

                <div class="chart-lg"><canvas id="chartMain"></canvas></div>
            </div>

            <div class="panel panel-split">

                <div class="half" style="display:flex; gap:15px; ">
                    <div style="flex:1; direction: rtl;">
                        <div class="metric-top">
                            <div class="metric-number">33</div>
                            <div class="metric-change green">▲ 18.02%</div>
                        </div>
                        <div class="description">اجمالي الطلبات</div>
                        <div class="chart-sm"><canvas id="chartSmall1"></canvas></div>
                    </div>

                    <div style="flex:1;direction: rtl;">
                        <div class="metric-top">
                            <div class="metric-number">86k</div>
                            <div class="metric-change green">▲ 18.02%</div>
                        </div>
                        <div class="description">اجمالي المستخدمين</div>
                        <div class="chart-sm"><canvas id="chartSmall2"></canvas></div>
                    </div>
                </div>

                <div class="half" style="display:flex; gap:20px; align-items:center;">
                    <div style="flex:1;">
                        <div class="heading">المبيعات & الارباح</div>
                        <div class="metric-top" style="margin-top:10px;">
                            <div class="metric-number" style="font-size:32px;">16,869</div>
                            <div class="metric-change green">↑ 2.87%</div>
                        </div>
                        <div class="description">إجمالي عدد المبيعات ونسبة الارباح ضمن النطاق الزمني المحدد.</div>
                    </div>
                    <div style="flex:1; height:140px;"><canvas id="chartSessions"></canvas></div>
                </div>

            </div>

        </div>

        <!-- ================= BOTTOM SECTION ================= -->

        <div class="bottom-grid">

            <!-- LEFT -->
            <div class="panel" dir="rtl">

                <div class="heading">إحصائيات</div>
                <div class="description">
                    احصائيات اداء الموقع الالكتروني.
                </div>
                <div style="display: flex; justify-content: space-between; padding-left: 40px;">
                    <div class="acq-row">
                        <div class="acq-box " style="background-color: #14b8a6;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <rect x="3" y="10" width="3" height="10" />
                                <rect x="9" y="6" width="3" height="14" />
                                <rect x="15" y="3" width="3" height="17" />
                            </svg>
                        </div>
                        <div>
                            <div class="meta-title">ألطلبات</div>
                            <div class="meta-value">33</div>
                        </div>
                    </div>

                    <div class="acq-row">
                        <div class="acq-box purple">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <rect x="3" y="10" width="3" height="10" />
                                <rect x="9" y="6" width="3" height="14" />
                                <rect x="15" y="3" width="3" height="17" />
                            </svg>
                        </div>
                        <div>
                            <div class="meta-title">الارباح</div>
                            <div class="meta-value">16,869</div>
                        </div>
                    </div>
                </div>

                <hr style="margin:25px 0; border:none; border-top:1px solid #eee;">

                <div class="heading">Sessions</div>
                <div class="description">User engagement period.</div>

                <div class="session-circles">
                    <div class="circle-box">
                        <canvas id="circle1"></canvas>
                        <div>
                            <div class="meta-title">% New Sessions</div>
                            <div class="meta-value">26.80%</div>
                        </div>
                    </div>

                    <div class="circle-box">
                        <canvas id="circle2"></canvas>
                        <div>
                            <div class="meta-title">Pages/Session</div>
                            <div class="meta-value">1,005</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="panel" dir="rtl">
                <div class="heading">حالة الطلبات</div>

                <div class="channel-grid">


                    <div style="display: flex;flex-direction: column;justify-content: space-between;">
                        <div class="channel-item">
                            <div>قيد المعالجة</div>
                            <div>1,320 (25%)</div>
                        </div>
                        <div class="bar"><span style="width:25%; background:#6c5ce7;"></span></div>

                        <div class="channel-item">
                            <div>تم الشحن</div>
                            <div>987 (20%)</div>
                        </div>
                        <div class="bar"><span style="width:20%; background:#3b82f6;"></span></div>

                        <div class="channel-item">
                            <div>مكتمل</div>
                            <div>2,010 (30%)</div>
                        </div>
                        <div class="bar"><span style="width:30%; background:#14b8a6;"></span></div>

                        <div class="channel-item">
                            <div>ملغي</div>
                            <div>654 (15%)</div>
                        </div>
                        <div class="bar"><span style="width:15%; background:#0ea5e9;"></span></div>
                    </div>
                    <div class="donut-wrap">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
     
@endsection
@section('jsfile')
    <script src="assets/js/chart.js"></script> 
@endsection