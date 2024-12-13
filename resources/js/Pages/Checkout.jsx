import React, { useEffect, useState } from 'react';
import { Button, Input, Radio, Typography, message, List, Modal, Form, Select } from 'antd';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons';

const { Title, Text } = Typography;
const { Option } = Select;

const Checkout = () => {
  const [cart, setCart] = useState(() => {
    const LocalCarts = sessionStorage.getItem('checkoutItems');
    if (LocalCarts) {
      return JSON.parse(LocalCarts);
    } else {
      return []; // Giỏ hàng mặc định là mảng trống
    }
  });
  useEffect(() => {
    // Nếu giỏ hàng đã bị xóa hoặc người dùng quay lại, lấy lại giỏ hàng từ sessionStorage
    const LocalCarts = sessionStorage.getItem('checkoutItems');
    if (LocalCarts) {
      setCart(JSON.parse(LocalCarts));
    }
  }, []);

  const [voucher, setVoucher] = useState('');
  const [shippingFee] = useState(32700);
  const discount = voucher === 'SHOPEE20' ? 20000 : 0;
  const totalPrice = cart.reduce((total, item) => total + item.price * item.quantity, 0);
  const totalAmount = totalPrice + shippingFee - discount;

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [address, setAddress] = useState(null);
  const [newAddress, setNewAddress] = useState({ name: '', phone: '', street: '' });
  const [availableAddresses, setAvailableAddresses] = useState([]);
  const [paymentMethod, setPaymentMethod] = useState('');
  const [loading, setLoading] = useState(false);

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const formatCurrency = (value) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

  const fetchAddresses = async () => {
    try {
      const response = await fetch('/api/address', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      });

      if (!response.ok) throw new Error('Không thể tải danh sách địa chỉ.');

      const data = await response.json();
      setAvailableAddresses(data);
      if (data.length > 0) setAddress(data[0].id);
    } catch (error) {
      console.error(error);
      message.error('Lỗi khi tải địa chỉ. Vui lòng thử lại!');
    }
  };

  useEffect(() => {
    fetchAddresses();
  }, []);

  const handleOrder = async () => {
    if (cart.length === 0) {
      message.error('Giỏ hàng trống, không thể đặt hàng.');
      return;
    }
    if (!address || !paymentMethod) {
      message.error('Vui lòng chọn địa chỉ và phương thức thanh toán!');
      return;
    }

    const selectedAddress = availableAddresses.find((addr) => addr.id === address);
    const orderData = { cart, address: selectedAddress, paymentMethod, totalAmount };

    setLoading(true);

    try {
      // Gửi yêu cầu tạo đơn hàng
      const orderResponse = await fetch('/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(orderData),
      });

      if (!orderResponse.ok) {
        throw new Error('Lỗi khi tạo đơn hàng.');
      }

      const orderResult = await orderResponse.json();
      const orderId = orderResult.id;

      if (paymentMethod === 'bank') {
        // Nếu thanh toán qua ngân hàng, tạo link thanh toán VNPay
        const paymentResponse = await fetch('vnpay/create-payment-link', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ ...orderData, order_id: orderId }),
        });

        if (!paymentResponse.ok) {
          throw new Error('Lỗi khi tạo link thanh toán.');
        }

        const paymentResult = await paymentResponse.json();
        const vnpayUrl = paymentResult.paymentUrl;

        // Chuyển hướng người dùng đến VNPay để thanh toán
        window.location.href = vnpayUrl;
      } else {
        message.success('Đặt hàng thành công!');
        setCart([]);
        sessionStorage.removeItem('checkoutItems');
      }
    } catch (error) {
      console.error(error);
      message.error('Lỗi khi đặt hàng. Vui lòng thử lại!');
    } finally {
      setLoading(false);
    }
  };



  const handleAddAddress = async () => {
    if (!newAddress.name || !newAddress.phone || !newAddress.street) {
      message.error('Vui lòng nhập đầy đủ thông tin địa chỉ.');
      return;
    }

    setLoading(true);
    try {
      const response = await fetch('/api/address', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(newAddress),
      });

      if (!response.ok) throw new Error('Không thể thêm địa chỉ mới.');

      const data = await response.json();
      setAvailableAddresses((prev) => [...prev, data]);
      setAddress(data.id);
      setNewAddress({ name: '', phone: '', street: '' });
      setIsModalOpen(false);
      message.success('Thêm địa chỉ thành công!');
    } catch (error) {
      console.error(error);
      message.error('Lỗi khi thêm địa chỉ. Vui lòng thử lại!');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-5 max-w-3xl mx-auto border border-gray-300 rounded-lg">
      <Title level={2} align="center">Giỏ Hàng</Title>

      {/* Địa chỉ nhận hàng */}
      <div className="mb-4">
        <Title level={4}>Địa Chỉ Nhận Hàng</Title>
        <Select
          style={{ width: '100%' }}
          value={address}
          onChange={(value) => setAddress(value)}
          placeholder="Chọn địa chỉ giao hàng"
        >
          {availableAddresses.map((addr) => (
            <Option key={addr.id} value={addr.id}>
              {`${addr.name} - ${addr.phone}, ${addr.street}`}
            </Option>
          ))}
        </Select>
        <Button type="dashed" className="mt-3" onClick={() => setIsModalOpen(true)}>
          Thêm địa chỉ mới
        </Button>
      </div>

      {/* Danh sách sản phẩm */}
      <List
        bordered
        dataSource={cart}
        renderItem={(item) => (
          <List.Item>
            <div>
              <Text>{item.name}</Text>
              <br />
              <Text>Số lượng: {item.quantity}</Text>
              <br />
              <Text>Giá: {formatCurrency(item.price)}</Text>
            </div>
          </List.Item>
        )}
      />

      {/* Phương thức thanh toán */}
      <div className="mt-5">
        <Title level={4}>Phương Thức Thanh Toán</Title>
        <Radio.Group value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)}>
          <Radio value="cash">Thanh toán khi nhận hàng</Radio>
          <Radio value="bank">Thanh toán qua ngân hàng</Radio>
        </Radio.Group>
      </div>

      {/* Tổng tiền */}
      <div className="mt-5 font-bold">
        <Text>Tổng tiền hàng: {formatCurrency(totalPrice)}</Text>
        <br />
        <Text>Phí vận chuyển: {formatCurrency(shippingFee)}</Text>
        <br />
        <Text>Voucher giảm giá: -{formatCurrency(discount)}</Text>
        <br />
        <Text>Tổng thanh toán: {formatCurrency(totalAmount)}</Text>
      </div>

      {/* Nhập Voucher */}
      <Input
        placeholder="Nhập Voucher"
        value={voucher}
        onChange={(e) => setVoucher(e.target.value)}
        className="mt-5"
      />

      <Button
        type="primary"
        className="mt-5"
        style={{ width: '100%' }}
        onClick={handleOrder}
        loading={loading}
      >
        <FontAwesomeIcon icon={faCheckCircle} /> Đặt hàng
      </Button>

      {/* Modal Thêm địa chỉ */}
      <Modal
        title="Thêm địa chỉ mới"
        open={isModalOpen}
        onCancel={() => setIsModalOpen(false)}
        footer={null}
        className="rounded-lg shadow-lg"
      >
        <Form onFinish={handleAddAddress} className="space-y-4">
          <Form.Item
            label={<span className="text-lg font-semibold">Họ tên</span>}
            required
            className="flex flex-col"
          >
            <Input
              value={newAddress.name}
              onChange={(e) => setNewAddress({ ...newAddress, name: e.target.value })}
              className="border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300"
              placeholder="Nhập họ tên"
            />
          </Form.Item>
          <Form.Item
            label={<span className="text-lg font-semibold">Số điện thoại</span>}
            required
            className="flex flex-col"
          >
            <Input
              value={newAddress.phone}
              onChange={(e) => setNewAddress({ ...newAddress, phone: e.target.value })}
              className="border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300"
              placeholder="Nhập số điện thoại"
            />
          </Form.Item>
          <Form.Item
            label={<span className="text-lg font-semibold">Địa chỉ</span>}
            required
            className="flex flex-col"
          >
            <Input
              value={newAddress.street}
              onChange={(e) => setNewAddress({ ...newAddress, street: e.target.value })}
              className="border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300"
              placeholder="Nhập địa chỉ"
            />
          </Form.Item>
          <Button
            type="primary"
            htmlType="submit"
            loading={loading}
            className="w-full bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-2"
          >
            Thêm Địa Chỉ
          </Button>
        </Form>
      </Modal>

    </div>
  );
};

export default Checkout;
