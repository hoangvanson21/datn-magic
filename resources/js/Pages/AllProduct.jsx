import React, { useState, useEffect } from 'react';
import { Layout, Select, Checkbox, Slider, Button, Card, Row, Col, Typography } from 'antd';
import { ShopOutlined, FilterOutlined, DollarOutlined, StarOutlined, ReloadOutlined } from '@ant-design/icons';
import ProductCard from '@/Components/ProductCard';
import PageTitle from './Layouts/TitlePage';

const { Content } = Layout;
const { Title } = Typography;

const ProductPage = ({ products = [], brands = [], categories = [] }) => {
    const minPrice = products.length > 0 ? Math.min(...products.map(product => product.price)) : 0;
    const maxPrice = products.length > 0 ? Math.max(...products.map(product => product.price)) : 20;

    const [filteredProducts, setFilteredProducts] = useState(products);
    const [priceRange, setPriceRange] = useState([minPrice, maxPrice]);
    const [categoryFilters, setCategoryFilters] = useState([]);
    const [ratingFilters, setRatingFilters] = useState([]);
    const [brandFilters, setBrandFilters] = useState([]);
    const [sortOrder, setSortOrder] = useState('');

    useEffect(() => {
        filterAndSortProducts();
    }, [priceRange, categoryFilters, ratingFilters, brandFilters, sortOrder, products]);

    const filterByCategory = (products) => {
        if (categoryFilters.length > 0) {
            return products.filter(product =>
                product.category && categoryFilters.includes(product.category.name)
            );
        }
        return products;
    };

    const filterByRating = (products) => {
        if (ratingFilters.length > 0) {
            return products.filter(product =>
                ratingFilters.includes(product.rating)
            );
        }
        return products;
    };

    const filterByBrand = (products) => {
        if (brandFilters.length > 0) {
            return products.filter(product =>
                product.brand && brandFilters.includes(product.brand.name)
            );
        }
        return products;
    };

    const sortProducts = (products) => {
        if (sortOrder === 'asc') {
            return products.sort((a, b) => a.price - b.price);
        } else if (sortOrder === 'desc') {
            return products.sort((a, b) => b.price - a.price);
        }
        return products;
    };

    const filterAndSortProducts = () => {
        let updatedProducts = [...products];

        updatedProducts = updatedProducts.filter(product => product.price >= priceRange[0] && product.price <= priceRange[1]);

        updatedProducts = filterByCategory(updatedProducts);
        updatedProducts = filterByRating(updatedProducts);
        updatedProducts = filterByBrand(updatedProducts);
        updatedProducts = sortProducts(updatedProducts);

        setFilteredProducts(updatedProducts);
    };

    const handleSortChange = (value) => {
        setSortOrder(value);
    };

    const handlePriceChange = (value) => {
        setPriceRange(value);
    };

    const handleCategoryChange = (checkedValues) => {
        setCategoryFilters(checkedValues);
    };

    const handleRatingChange = (checkedValues) => {
        setRatingFilters(checkedValues);
    };

    const handleBrandChange = (checkedValues) => {
        setBrandFilters(checkedValues);
    };

    const resetFilters = () => {
        setPriceRange([minPrice, maxPrice]);
        setCategoryFilters([]);
        setRatingFilters([]);
        setBrandFilters([]);
        setSortOrder('');
        setFilteredProducts(products);
    };

    return (
        <>
            <PageTitle title="Tất cả sản phẩm" />
            <Layout className="container mx-auto py-8 px-4">
                <Row gutter={24}>
                    <Col xs={24} md={6}>
                        <Card title={<><FilterOutlined className="mr-2" /> Bộ lọc sản phẩm</>} bordered={false}>
                            <div className="mb-4">
                                <h3>Sắp xếp theo <ShopOutlined className="ml-2" /></h3>
                                <Select
                                    onChange={handleSortChange}
                                    className="w-full"
                                    placeholder="Chọn sắp xếp"
                                >
                                    <Select.Option value="asc">Giá tăng dần</Select.Option>
                                    <Select.Option value="desc">Giá giảm dần</Select.Option>
                                </Select>
                            </div>
                            <div className="mb-4">
                                <h3>Danh mục</h3>
                                <Checkbox.Group
                                    options={categories.map(category => ({ label: category.name, value: category.name }))}
                                    value={categoryFilters}
                                    onChange={handleCategoryChange}
                                    className="w-full"
                                />
                            </div>
                            <div className="mb-4">
                                <h3>Thương hiệu</h3>
                                <Checkbox.Group
                                    options={brands.map(brand => ({ label: brand.name, value: brand.name }))}
                                    value={brandFilters}
                                    onChange={handleBrandChange}
                                    className="w-full"
                                />
                            </div>
                            <div className="mb-4">
                                <h3>Giá <DollarOutlined className="ml-2" /></h3>
                                <Slider
                                    range
                                    min={minPrice}
                                    max={maxPrice}
                                    value={priceRange}
                                    onChange={handlePriceChange}
                                    marks={{
                                        [minPrice]: `${minPrice}đ`,
                                        [maxPrice]: `${maxPrice}đ`,
                                    }}
                                />
                                <div className="flex justify-between text-gray-600 mt-2">
                                    <span>{minPrice}đ</span>
                                    <span>{maxPrice}đ</span>
                                </div>
                            </div>

                            <Button onClick={resetFilters} type="primary" icon={<ReloadOutlined />} className="w-full">
                                Đặt lại bộ lọc
                            </Button>
                        </Card>
                    </Col>
                    <Col xs={24} md={18}>
                        <Card title="Sản phẩm" bordered={false}>
                            <div
                                style={{
                                    maxHeight: 'calc(100vh - 10px)',
                                    overflowY: 'auto',
                                    display: 'flex',
                                    flexWrap: 'wrap',
                                    gap: '15px',
                                    justifyContent: 'center', // Căn giữa các sản phẩm
                                }}
                            >
                                {filteredProducts.length > 0 ? (
                                    filteredProducts.map((product) => (
                                        <div key={product.id} style={{ flex: '1 0 30%' }}>
                                            <ProductCard product={product} />
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-center text-gray-500" style={{ width: '100%' }}>
                                        Không có sản phẩm nào để hiển thị.
                                    </p>
                                )}
                            </div>

                        </Card>
                    </Col>
                </Row>
            </Layout>
        </>
    );
};

export default ProductPage;
