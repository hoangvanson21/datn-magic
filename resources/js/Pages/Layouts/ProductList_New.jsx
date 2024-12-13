import React from 'react';
import ProductCard from '../../Components/ProductCard';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const ProductList = ({ products_news }) => {
    return (
        <>
            <br />
            {/* New Products Section */}
            <div className="product__new flex items-center">
                <h1 className="text-3xl  text-red-600 mx-auto container">
                    SẢN PHẨM MỚI NHẤT
                </h1>
            </div>

            {/* Product List Container */}
            <div className="product-list container mx-auto p-6 rounded-lg shadow-lg border bg-white">
                <div className="product-list__body grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {products_news.length > 0 ? (
                        products_news.map((product) => (
                            <div
                                key={product.id}
                                className="transform transition duration-300 hover:scale-105 hover:shadow-xl hover:bg-gray-50 hover:opacity-90 ease-in-out"
                            >
                                <ProductCard product={product} />
                            </div>
                        ))
                    ) : (
                        <p className="col-span-1 sm:col-span-2 lg:col-span-4 text-gray-500 text-center">
                            Không có sản phẩm nào để hiển thị.
                        </p>
                    )}
                </div>
            </div>
        </>
    );
};

export default ProductList;
