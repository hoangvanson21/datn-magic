import React, { useState, useEffect, useMemo, useCallback } from 'react';
import { Button, Checkbox, List, InputNumber, Typography, message } from 'antd';

const { Title, Text } = Typography;

const CartList = ({ cartItems }) => {
    const [selectedItems, setSelectedItems] = useState([]);
    const [selectAll, setSelectAll] = useState(false);
    const [totalPrice, setTotalPrice] = useState(0);
    const [allProducts, setAllProducts] = useState(cartItems);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const calculateTotalPrice = useMemo(() => {
        return allProducts.reduce((acc, item, index) => {
            return selectedItems[index] ? acc + item.price * item.quantity : acc;
        }, 0);
    }, [selectedItems, allProducts]);

    useEffect(() => {
        setTotalPrice(calculateTotalPrice);
    }, [calculateTotalPrice]);

    const showMessage = (msg, type = 'success') => {
        message[type](msg);
    };

    const handleSelectAll = () => {
        const newSelectedItems = new Array(allProducts.length).fill(!selectAll);
        setSelectedItems(newSelectedItems);
        setSelectAll(!selectAll);
    };

    const handleItemSelect = (index) => {
        const newSelectedItems = [...selectedItems];
        newSelectedItems[index] = !newSelectedItems[index];
        setSelectedItems(newSelectedItems);
    };

    const handleRemoveSelected = async () => {
        const idsToRemove = allProducts.filter((_, index) => selectedItems[index]).map(item => item.id);
        if (idsToRemove.length > 0) {
            const updatedProducts = allProducts.filter(item => !idsToRemove.includes(item.id));
            setAllProducts(updatedProducts);
            setSelectedItems(new Array(updatedProducts.length).fill(false));
            setSelectAll(false);
            showMessage('Xóa sản phẩm thành công.');
            try {
                const response = await fetch(`/api/cart/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken,
                    },
                    body: JSON.stringify({ ids: idsToRemove }),
                });
                if (!response.ok) {
                    showMessage('Xóa sản phẩm không thành công.', 'error');
                }
            } catch (error) {
                console.error('Error removing products:', error);
                showMessage('Đã xảy ra lỗi khi xóa sản phẩm.', 'error');
            }
        } else {
            showMessage('Vui lòng chọn sản phẩm để xóa.', 'warning');
        }
    };

    const updateQuantity = useCallback(
        async (itemId, newQuantity) => {
            if (newQuantity < 1) return;
            const updatedProducts = allProducts.map(item => (
                item.id === itemId ? { ...item, quantity: newQuantity } : item
            ));
            setAllProducts(updatedProducts);
            try {
                const response = await fetch(`/api/cart/quantity`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken,
                    },
                    body: JSON.stringify({ id: itemId, quantity: newQuantity }),
                });
                if (!response.ok) {
                    showMessage('Cập nhật số lượng không thành công.', 'error');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        },
        [csrfToken, allProducts]
    );

    const handleCheckout = () => {
        const selectedItemsToCheckout = allProducts.filter((_, index) => selectedItems[index]).map(item => ({
            id: item.product.id,
            name: item.product.name,
            price: item.price,
            quantity: item.quantity,
            option: item.option_name,
            color: item.color_name,
        }));
        if (selectedItemsToCheckout.length > 0) {
            sessionStorage.setItem('checkoutItems', JSON.stringify(selectedItemsToCheckout));
            window.location.href = `${window.location.origin}/checkout`;
        } else {
            showMessage('Vui lòng chọn sản phẩm để thanh toán', 'warning');
        }
    };

    return (
        <>
            <div className="container mx-auto px-6 py-10 bg-white rounded-lg shadow-md">
                <Title level={2} className="text-center mb-8"><i className="fas fa-shopping-cart"></i> Giỏ hàng của bạn</Title>
                <div className="flex justify-between mb-6">
                    <Button onClick={handleSelectAll} type="primary" style={{ backgroundColor: '#4CAF50', borderColor: '#4CAF50' }} className="hover:bg-green-700 transition-colors duration-300">
                        {selectAll ? <i className="fas fa-times"></i> : <i className="fas fa-check"></i>} {selectAll ? 'Bỏ chọn tất cả' : 'Chọn tất cả'}
                    </Button>
                    <Button
                        onClick={handleRemoveSelected}
                        type="danger"
                        style={{ backgroundColor: '#f5222d', borderColor: '#f5222d', color: '#fff' }}
                        className="hover:bg-red-700 transition-colors duration-300"
                    >
                        <i className="fas fa-trash-alt"></i> Xóa đã chọn
                    </Button>
                </div>

                {allProducts.length > 0 ? (
                    <List
                        itemLayout="vertical"
                        dataSource={allProducts}
                        renderItem={(item, index) => (
                            <List.Item
                                key={item.id}
                                className="bg-gray-100 p-4 rounded-lg mb-4 shadow-sm hover:shadow-md transition-shadow duration-300"
                            >
                                <Checkbox
                                    checked={selectedItems[index]}
                                    onChange={() => handleItemSelect(index)}
                                    className="mr-3"
                                />
                                <List.Item.Meta
                                    avatar={
                                        <img
                                            src={`/storage/${item.product?.images || '/default-image.jpg'}`}
                                            alt={item.product?.name}
                                            className="rounded-lg"
                                            style={{ width: 100, height: 100 }}
                                        />
                                    }
                                    title={<Text strong>{item.product?.name}</Text>}
                                    description={
                                        <div className="flex flex-col">
                                            <div className="flex items-center mb-2">
                                                <Text strong>Số lượng:</Text>
                                                <InputNumber
                                                    min={1}
                                                    value={item.quantity}
                                                    onChange={value => updateQuantity(item.id, value)}
                                                    className="ml-2"
                                                />
                                                <Text className="text-red-500 ml-4">
                                                    {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price)}
                                                </Text>
                                            </div>
                                            <div className="flex items-center mb-2">
                                                <Text strong>Tùy chọn:</Text>
                                                <Text className="ml-2">{item.option_name}</Text>
                                            </div>
                                            <div className="flex items-center mb-2">
                                                <Text strong>Màu sắc:</Text>
                                                <Text className="ml-2">{item.color_name}</Text>
                                            </div>
                                        </div>
                                    }
                                />
                            </List.Item>
                        )}
                    />
                ) : (
                    <Text type="secondary" className="text-center">Giỏ hàng của bạn hiện tại trống</Text>
                )}
                <div className="mt-10 flex justify-between items-center">
                    <Title level={3}><i className="fas fa-money-bill-wave"></i> Tổng tiền: {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(totalPrice)}</Title>
                    <Button onClick={handleCheckout} type="primary" size="large" style={{ backgroundColor: '#4CAF50', borderColor: '#4CAF50' }} className="hover:bg-green-700 transition-colors duration-300">
                        <i className="fas fa-credit-card"></i> Thanh toán
                    </Button>
                </div>
            </div>
        </>
    );
};

export default CartList;
