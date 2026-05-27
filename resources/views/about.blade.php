@extends(auth()->user()->role === 'Teacher' ? 'layouts.teacher' : (auth()->user()->role === 'Student' ? 'layouts.student' : 'layouts.app'))

@section('main')
<div class="container my-2">

    {{-- Page Header --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="color:#640d3c;">About Us</h1>
        <p class="text-muted fs-5">Shaping Young Minds for a Brighter Future</p>
    </div>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background-color:#E8D8FF; padding:10px; border-radius:8px;">
        <li class="breadcrumb-item">
        </li>
        <li class="breadcrumb-item active" aria-current="page" style="color:#333;">About us</li>
    </ol>
    </nav>

    {{-- Card Section --}}
    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="row g-0">

            {{-- Left Banner / Image Section --}}
            <div class="col-lg-5 d-none d-lg-block" 
                 style="background: linear-gradient(rgba(121,82,179,0.85), rgba(121,82,179,0.85)), url('{{ asset('images/school-campus.jpg') }}') center/cover no-repeat;">
                <div class="h-100 d-flex align-items-center justify-content-center text-white p-5">
                    <div>
                        <h2 class="fw-bold">Freshora School</h2>
                        <p class="fs-5">Excellence in Academics, Values, and Life Skills</p>
                    </div>
                </div>
            </div>

            {{-- Right Content Section --}}
            <div class="col-lg-7 p-5 bg-white">
                <p class="fs-5 text-muted">
                    Freshora School is built on the vision of shaping young minds for a brighter future by blending academics, values, and life skills. Our mission is to create confident, knowledgeable, and responsible citizens through quality education. With a team of highly qualified teachers, we provide personalized guidance and ensure that every student learns in a supportive and inspiring environment. 
                </p>
                <p class="fs-5 text-muted">
                    Our classrooms are equipped with smart boards and digital tools, encouraging curiosity, creativity, and critical thinking. We offer a wide range of academic programs aligned with global standards, training students not only for exams but also for real-life problem solving. Equal importance is given to sports, arts, culture, and co-curricular activities, with excellent facilities that build discipline, teamwork, and self-expression.
                </p>
                <p class="fs-5 text-muted">
                    At Freshora, we celebrate diversity and promote respect for different cultures and traditions. The campus is safe and secure, with CCTV monitoring, dedicated staff, and counseling support for emotional well-being. Parents are valued partners in the learning journey, with regular meetings, updates, and access to a digital portal to track attendance, grades, and progress. 
                </p>
                <p class="fs-5 text-muted">
                    Our library, science labs, and computer labs provide hands-on learning and access to knowledge, while science fairs, innovation projects, and cultural festivals encourage students to showcase their talents.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 15px;
}
h1, h2 {
    font-family: 'Poppins', sans-serif;
}
.fs-5 {
    line-height: 1.7;
}
@media (max-width: 768px) {
    .card .col-lg-5 {
        display: none;
    }
}
</style>
@endpush
