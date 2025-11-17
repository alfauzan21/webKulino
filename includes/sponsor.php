<main class="max-w-6xl mx-auto px-4 py-10">
    <section>
        <h3 class="text-xl font-semibold mb-4">Sponsor</h3>
        <div class="relative">
            <div id="sponsorSlider" class="flex overflow-x-auto gap-6 scroll-smooth no-scrollbar">
                <?php
                include("./includes/koneksi.php");
                $sql = mysqli_query($koneksi, "SELECT * FROM tb_sponsor ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($sql)) {
                ?>
                    <!-- Card Sponsor -->
                    <article
                        onclick="window.open('<?= htmlspecialchars($row['link']) ?>', '_blank')"
                        class="group relative bg-white rounded-2xl shadow-md min-w-[180px] md:min-w-[220px] overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer flex items-center justify-center">

                        <!-- Gambar Sponsor -->
                        <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>"
                            alt="<?= htmlspecialchars($row['nama']) ?>"
                            class="object-contain h-32 w-full grayscale group-hover:grayscale-0 transition duration-300 p-6" />

                        <!-- Overlay Hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent 
                        opacity-0 group-hover:opacity-100 translate-y-10 group-hover:translate-y-0 
                        transition-all duration-500 flex items-end justify-center pb-4">
                            <button class="bg-white text-gray-800 font-semibold text-sm px-4 py-2 rounded-full shadow-md hover:scale-105 transition">
                                Preview
                            </button>
                        </div>
                    </article>
                <?php } ?>
            </div>

            <!-- Tombol Navigasi -->
            <button onclick="scrollSlider('sponsorSlider', -1)"
                class="absolute top-1/2 -left-4 transform -translate-y-1/2 bg-white shadow-md rounded-full p-2 hover:bg-gray-100">
                ←
            </button>
            <button onclick="scrollSlider('sponsorSlider', 1)"
                class="absolute top-1/2 -right-4 transform -translate-y-1/2 bg-white shadow-md rounded-full p-2 hover:bg-gray-100">
                →
            </button>
        </div>
    </section>
</main>

<script>
    function scrollSlider(id, direction) {
        const slider = document.getElementById(id);
        const scrollAmount = 300;
        slider.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>