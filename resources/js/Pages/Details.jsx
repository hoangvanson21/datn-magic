import React, { useEffect, useState } from 'react';
import { Button, Typography, Row, Col, Card, Carousel, Tag, Space, notification } from 'antd';
import '../../css/Details.css';
import Comment from '@/Components/Comment';

const { Title, Text } = Typography;

const ProductDetail = ({ productData }) => {
    const [product, setProduct] = useState(productData || {});
    const [quantity, setQuantity] = useState(1);
    const [selectedOptionId, setSelectedOptionId] = useState(null);
    const [selectedColorId, setSelectedColorId] = useState(null);
    const [totalPrice, setTotalPrice] = useState(productData?.base_price || 0);

    // Cập nhật product khi có dữ liệu mới
    useEffect(() => {
        if (productData) {
            setProduct(productData);
        }
    }, [productData]);

    useEffect(() => {
        const selectedVariant = product.variants?.find(
            (variant) =>
                variant.option?.id === selectedOptionId &&
                variant.color?.id === selectedColorId
        );

        let newTotalPrice = 0;

        if (selectedVariant) {
            newTotalPrice = selectedVariant.variant_price * quantity;
        } else {
            newTotalPrice = product.base_price * quantity;
        }

        setTotalPrice(newTotalPrice);

    }, [selectedOptionId, selectedColorId, product.base_price, product.variants, quantity]);
    const handleCheckout = () => {
        // Create a payload with the current product and its details
        const checkoutProduct = {
            id: product.id,
            name: product.name,
            price: totalPrice,
            quantity,
            option: product.variants?.find(variant => variant.option.id === selectedOptionId)?.option,
            color: product.variants?.find(variant => variant.color.id === selectedColorId)?.color,
        };

        // Check if the user has selected all necessary options (variant and color)
        if (!checkoutProduct.option || !checkoutProduct.color) {
            notification.warning({
                message: 'Chọn đầy đủ thông tin',
                description: 'Vui lòng chọn phiên bản và màu sắc trước khi tiếp tục.',
            });
            return;
        }

        // Store the checkout product in sessionStorage or state
        sessionStorage.setItem('checkoutItems', JSON.stringify([checkoutProduct]));

        // Redirect to the checkout page
        window.location.href = `${window.location.origin}/checkout`;
    };

    // Thêm sản phẩm vào giỏ hàng
    const handleAddToCart = async () => {
        if (!selectedOptionId || !selectedColorId) {
            notification.warning({
                message: 'Chọn đầy đủ thông tin',
                description: 'Vui lòng chọn phiên bản và màu sắc trước khi thêm vào giỏ hàng.',
            });
            return;
        }

        const payload = {
            product_id: product.id,
            option_id: selectedOptionId,
            color_id: selectedColorId,
            quantity,
            price: totalPrice,
        };

        try {
            const response = await fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const result = await response.json();
                notification.success({
                    message: 'Thành công!',
                    description: result.success,
                });
            } else {
                const error = await response.json();
                notification.error({
                    message: 'Thất bại',
                    description: error.error || 'Không thể thêm vào giỏ hàng.',
                });
            }
        } catch (error) {
            notification.error({
                message: 'Lỗi hệ thống',
                description: 'Không thể kết nối tới máy chủ.',
            });
        }
    };

    return (
        <div className="container py-6">
            <Row gutter={[16, 16]}>
                {/* Hình ảnh sản phẩm */}
                <Col xs={24} md={12}>
                    <Card className="shadow-lg rounded-lg">
                        <Carousel autoplay>
                            {product.images?.map((image, index) => (
                                <div key={index} className="product-carousel-item">
                                    <img
                                        src={`/storage/${image}`}
                                        alt={product.name}
                                        className="rounded-lg w-full h-[400px] object-cover"
                                    />
                                </div>
                            )) || (
                                    <div className="flex items-center justify-center h-[400px] bg-gray-200">
                                        <span>Không có ảnh</span>
                                    </div>
                                )}
                        </Carousel>
                    </Card>
                </Col>

                {/* Thông tin sản phẩm */}
                <Col xs={24} md={12}>
                    <div className="product-info flex flex-col gap-4">
                        <Title level={2} className="text-black">{product.name}</Title>

                        {/* Giá sản phẩm */}
                        <div className="text-lg font-bold text-red-600 flex items-center">
                            {product.dis_price && (
                                <Text className="line-through text-gray-500 mr-2">
                                    {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.base_price)}
                                </Text>
                            )}
                            <Text>
                                {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(totalPrice)}
                            </Text>
                        </div>

                        {/* Tùy chọn RAM/ROM */}
                        <div className="flex gap-2">
                            {product.variants
                                ?.map((variant) => variant.option)
                                ?.filter((option, index, self) => self.findIndex(o => o.id === option.id) === index)
                                ?.map((option) => (
                                    <Tag
                                        key={option.id}
                                        color={selectedOptionId === option.id ? 'blue' : 'default'}
                                        className="cursor-pointer"
                                        onClick={() => setSelectedOptionId(option.id)}
                                    >
                                        {option.ram}GB / {option.rom}GB
                                    </Tag>
                                ))}
                        </div>

                        {/* Màu sắc */}
                        <div className="flex gap-2">
                            {product.variants
                                ?.map((variant) => variant.color)
                                ?.filter((color, index, self) => self.findIndex(c => c.id === color.id) === index)
                                ?.map((color) => (
                                    <Tag
                                        key={color.id}
                                        color={selectedColorId === color.id ? 'red' : 'default'}
                                        className="cursor-pointer"
                                        onClick={() => setSelectedColorId(color.id)}
                                    >
                                        {color.name}
                                    </Tag>
                                ))}
                        </div>

                        {/* Số lượng */}
                        <div className="flex items-center gap-2">
                            <Text className="font-semibold">Số lượng:</Text>
                            <input
                                type="number"
                                min={1}
                                value={quantity}
                                onChange={(e) => setQuantity(Number(e.target.value))}
                                className="border rounded-md px-2 py-1 w-20"
                            />
                        </div>

                        {/* Nút hành động */}
                        <Space direction="vertical" className="w-full">
                            <Button type="primary" size="large" className="w-full" onClick={handleAddToCart}>
                                Thêm vào giỏ hàng
                            </Button>
                            <Button onClick={handleCheckout} type="default" size="large" className="w-full">
                                Mua ngay
                            </Button>
                        </Space>
                    </div>
                </Col>
            </Row>

            {/* Mô tả sản phẩm */}
            <Row className="mt-8">
                <Col span={24}>
                    <Card className="shadow-sm">
                        <Title level={4}>Mô tả sản phẩm</Title>
                        <Text>{product.description || 'Thông tin sản phẩm đang được cập nhật.'}</Text>
                    </Card>
                </Col>
            </Row>

            {/* Bình luận */}
            <Comment product={product} />
        </div>
    );
};

export default ProductDetail;
