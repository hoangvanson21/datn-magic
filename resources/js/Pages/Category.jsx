import { Head } from '@inertiajs/react';
import ProductCard from '@/Components/ProductCard';
import { ShopOutlined, ExclamationCircleOutlined, TagsOutlined } from '@ant-design/icons';

const CateProducts = ({ data }) => {
    return (
        <>
            <Head title="Danh mục sản phẩm" />
            <div className="container mx-auto p-5 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 py-8">
                {data.brand ? (
                    <div className="category-section w-full bg-white bg-opacity-90 rounded-lg shadow-md p-6">
                        <h2 className="text-5xl font-extrabold text-blue-800 flex items-center justify-center my-6">
                            <ShopOutlined className="mr-3 text-5xl" />
                            {data.brand.name}
                        </h2>
                        {data.brand.products.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {data.brand.products.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                        ) : (
                            <div className="flex items-center justify-center h-32">
                                <p className="text-blue-800 flex items-center">
                                    <ExclamationCircleOutlined className="mr-2" />
                                    Không có sản phẩm nào.
                                </p>
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="all-products-section w-full py-8">
                        <h2 className="text-5xl font-extrabold text-white my-6 text-center">
                            Tất cả danh mục
                        </h2>
                        {data.brands.length > 0 ? (
                            data.brands.map((brand) => (
                                <div key={brand.id} className="brand-section my-8 bg-white bg-opacity-90 rounded-lg shadow-md p-6">
                                    <h3 className="text-4xl font-semibold text-green-800 flex items-center mb-4">
                                        <TagsOutlined className="mr-2 text-4xl" />
                                        {brand.name}
                                    </h3>
                                    {brand.products.length > 0 ? (
                                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                            {brand.products.map((product) => (
                                                <ProductCard key={product.id} product={product} />
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="flex items-center justify-center h-32">
                                            <p className="text-green-800 flex items-center">
                                                <ExclamationCircleOutlined className="mr-2" />
                                                Không có sản phẩm nào cho thương hiệu này.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="flex items-center justify-center h-32">
                                <p className="text-white flex items-center">
                                    <ExclamationCircleOutlined className="mr-2" />
                                    Không có thương hiệu nào.
                                </p>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
};

export default CateProducts;
