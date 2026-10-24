    </main>
    <footer class="bg-matteBlack text-white py-12">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4">
                <!-- White version of logo for dark footer -->
                <img src="logo/luga-pasal-logo.svg" alt="Luga Pasal" class="h-12 brightness-0 invert">
                <p class="text-gray-400 text-sm leading-relaxed">Nepal's premier destination for new and pre-loved high-quality apparel. Curating style for everyone.</p>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-white uppercase text-xs tracking-widest">Customer Care</h4>
                <ul class="text-gray-400 text-sm space-y-2">
                    <li><a href="#" class="hover:text-brandLime transition">Help Center</a></li>
                    <li><a href="#" class="hover:text-brandLime transition">Shipping Policy</a></li>
                    <li><a href="#" class="hover:text-brandLime transition">Returns & Refunds</a></li>
                    <li><a href="#" class="hover:text-brandLime transition">Contact Us</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-white uppercase text-xs tracking-widest">For Sellers</h4>
                <ul class="text-gray-400 text-sm space-y-2">
                    <li><a href="dashboard.php" class="hover:text-brandLime transition">Sell on Luga Pasal</a></li>
                    <li><a href="dashboard.php" class="hover:text-brandLime transition">Vendor Dashboard</a></li>
                    <li><a href="#" class="hover:text-brandLime transition">Pricing Plans</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-white uppercase text-xs tracking-widest">Newsletter</h4>
                <p class="text-gray-400 text-sm">Subscribe to get updates on new arrivals.</p>
                <form class="flex">
                    <input type="email" placeholder="Email" class="bg-gray-800 text-white px-3 py-2 rounded-l-md focus:outline-none w-full border-none">
                    <button class="bg-brandLime text-black px-4 py-2 rounded-r-md font-bold hover:bg-lime-400 transition">Join</button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-10 pt-6 text-center text-gray-500 text-xs">
            &copy; <?php echo date('Y'); ?> Luga Pasal. All rights reserved.
        </div>
    </footer>
</body>
</html>
