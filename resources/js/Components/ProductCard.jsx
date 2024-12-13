import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { Link } from '@inertiajs/inertia-react';
import { Card, Button, Rate, message, Tooltip } from 'antd';
import { HeartOutlined, ShoppingCartOutlined, EyeOutlined, EyeFilled } from '@ant-design/icons';

const { Meta } = Card;

const getRandomInt = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
const getRandomRating = () => getRandomInt(1, 5);
const getRandomPrice = () => getRandomInt(100000, 1000000);
const getRandomViews = () => getRandomInt(100, 10000);


const handleAddToFavorites = async (productId) => {
    try {
        const response = await fetch('/api/favorites', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ productId })
        });

        const data = await response.json();

        if (response.ok) {
            message.success(data.message);
        } else {
            message.error(data.message);
        }
    } catch (error) {
        console.error('Error adding to favorites', error);
        message.error('Lỗi xảy ra khi thêm vào yêu thích.');
    }
};



const ProductCard = ({ product }) => {
    const images = product.images ? JSON.parse(product.images) : [];
    const imageUrl = images.length > 0 ? `http://localhost:8000/storage/${images[0]}` : null;
    const productName = product.name || 'Sản phẩm không tên';
    // const productDescription = product.description || getRandomDescription();
    const productDescription = (() => {
        try {
            // Nếu `description` là JSON hợp lệ, chuyển đổi
            const parsedDescription = JSON.parse(product.description);
            return Array.isArray(parsedDescription) ? parsedDescription.join(' ') : parsedDescription;
        } catch {
            // Nếu không phải JSON, trả về chuỗi ban đầu
            return product.description || getRandomDescription();
        }
    })();

    const productReviews = product.reviews && product.reviews.length > 0 ? product.reviews : [];

    const fiveStarReviews = productReviews.filter(review => review.rating === 5);

    const totalFiveStarReviews = fiveStarReviews.length;

    const handleViewProduct = () => {
        axios.post(`/api/products/${product.id}/view`)
            .then((response) => {
                if (response.data.success) {
                    console.log(`Lượt xem cập nhật: ${response.data.view}`);
                }
            })
            .catch((error) => {
                console.error('Error updating view count:', error);
            });
    };
    return (
        <Card
            hoverable
            className="product-card shadow-lg rounded-lg overflow-hidden border border-gray-200 "
            cover={imageUrl ? <img alt={productName} src={imageUrl} className="product-image m-auto" /> :
                <div className="product-image-placeholder flex items-center justify-center bg-gray-200 h-48">
                    <span className="text-gray-400">Không có ảnh</span>
                </div>}
            actions={[
                <Button type="link" icon={<HeartOutlined />} onClick={() => handleAddToFavorites(product.id)} className="text-red-500 hover:text-red-600">
                    Yêu thích
                </Button>,

                <Link href={route('products.show', { id: product.id })} onClick={() => handleViewProduct(product.id)}>
                    <Button type="link" icon={<EyeOutlined />} className="text-green-500 hover:text-green-600">
                        Xem chi tiết
                    </Button>
                </Link>,
            ]}
        >
            <Meta
                description={
                    <>
                        <span className="product-name text-lg font-bold text-gray-900 truncate">{productName}</span>
                        <div
                            className="product-description text-sm text-gray-500 mb-2"
                            dangerouslySetInnerHTML={{
                                __html:
                                    productDescription.length > 80
                                        ? `${productDescription.substring(0, 60)}...`
                                        : productDescription,
                            }}
                        />
                        <p className="text-xl text-red-500 font-bold">
                            {product.dis_price ? (
                                <>
                                    {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.dis_price)}
                                    <span className="line-through text-gray-500 ml-2 text-sm">
                                        {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price)}
                                    </span>
                                </>
                            ) : (
                                new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price)
                            )}
                        </p>

                        <div className="product-views text-sm text-gray-500  flex items-center">
                            <EyeFilled className="mr-1" />
                            {product.view} lượt xem
                        </div>
                    </>
                }
            />
        </Card>
    );
};

ProductCard.propTypes = {
    product: PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string,
        description: PropTypes.string,
        price: PropTypes.number,
        image: PropTypes.string,
        badgeText: PropTypes.string,
        installment: PropTypes.bool,
        discount: PropTypes.bool,
        voucher: PropTypes.string,
        rating: PropTypes.number,
        views: PropTypes.number,
        reviews: PropTypes.arrayOf(
            PropTypes.shape({
                user: PropTypes.string,
                comment: PropTypes.string,
                rating: PropTypes.number,
            })
        ),
    }).isRequired,
};

export default ProductCard;
