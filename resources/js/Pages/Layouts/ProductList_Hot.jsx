import React from 'react';
import ProductCard from '../../Components/ProductCard';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const ProductList = ({ products }) => {
    return (
        <>
            <div className="product-list container mx-auto p-6 bg-gradient-to-r from-red-100 via-yellow-100 to-yellow-200 rounded-xl shadow-xl">
                {/* Section Title */}
                <div className="product__new mb-10 text-center p-6 rounded-lg shadow-md bg-white">
                    <h1 className="text-4xl  text-red-700 mb-4 uppercase tracking-wider">
                        Sản phẩm nổi bật
                    </h1>
                    <p className="text-lg text-gray-600 font-medium">
                        Khám phá các sản phẩm đang được yêu thích nhất ngay bây giờ!
                    </p>
                </div>

                {/* Product Grid */}
                <div className="product-list__body grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    {products.length > 0 ? (
                        products.map((product) => (
                            <div
                                key={product.id}
                                className="flex justify-center "
                            >
                                <ProductCard product={product} />
                            </div>
                        ))
                    ) : (
                        <p className="col-span-full text-gray-500 text-center text-xl font-medium">
                            Không có sản phẩm nào để hiển thị.
                        </p>
                    )}
                </div>
            </div>

            {/* Banner Section */}
            <div className="banner mt-10 flex flex-col lg:flex-row items-center bg-gradient-to-r from-yellow-200 to-red-200 rounded-xl shadow-xl p-8 container mx-auto">
                <img
                    src="/storage/images/xe.webp"
                    alt="Banner"
                    className="flex-none w-full lg:w-1/2 h-auto rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105 mb-6 lg:mb-0"
                />
                <div className="flex-1 ml-0 lg:ml-8 text-center lg:text-left">
                    <h2 className="text-4xl font-bold text-red-600 mb-6 tracking-wide transform transition-all duration-300 hover:scale-105">
                        Giao hàng 24 / 7
                    </h2>
                    <p className="text-lg text-gray-700 mb-4">
                        Chúng tôi cam kết giao hàng nhanh chóng, an toàn và tận tình phục vụ mọi lúc, mọi nơi.
                    </p>
                    <p className="text-lg text-gray-700 mb-6">
                        Hãy tận dụng các ưu đãi đặc biệt và dịch vụ chăm sóc khách hàng của chúng tôi. Đặt hàng ngay để nhận ưu đãi tốt nhất!
                    </p>
                    <button className="bg-red-600 text-white font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-red-500 transition-all duration-300">
                        Đặt hàng ngay
                    </button>
                </div>
            </div>
        </>
    );
};

export default ProductList;
