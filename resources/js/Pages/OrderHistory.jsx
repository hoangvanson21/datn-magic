import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Button, message, Spin } from 'antd';
import { EyeOutlined } from '@ant-design/icons';
import { Link, usePage } from '@inertiajs/react';

const OrderHistory = () => {
  const { props } = usePage();
  const user = props.auth.user;
  const [loading, setLoading] = useState(true);
  const [orders, setOrders] = useState([]);
  const [error, setError] = useState(null);

  // Format ngày giờ
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
  };

  // Call API to cancel the order
  const cancelOrder = async (orderId) => {
    try {
      const response = await axios.delete(`/api/orders/${orderId}`);
      if (response.status === 200) {
        message.success('Đơn hàng đã được hủy thành công');
        // Update the order list after cancellation
        setOrders(orders.filter(order => order.id !== orderId));
      } else {
        message.error('Không thể hủy đơn hàng');
      }
    } catch (error) {
      message.error('Đã xảy ra lỗi, vui lòng thử lại sau.');
    }
  };

  // Fetch orders for the logged-in user
  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const response = await axios.get(`/api/orders/history?user_id=${user.id}`);
        if (response.status === 200) {
          let fetchedOrders = response.data.orders;

          // Sort orders so that "Giao Hàng Thành công" orders go to the bottom
          fetchedOrders = fetchedOrders.sort((a, b) => {
            if (a.name_status === "Giao Hàng Thành công") return 1;
            if (b.name_status === "Giao Hàng Thành công") return -1;
            return 0;
          });

          setOrders(fetchedOrders);
          setLoading(false);
        } else {
          setError('Không thể tải đơn hàng.');
          setLoading(false);
        }
      } catch (error) {
        setError('Chưa có đơn hàng nào.');
        setLoading(false);
      }
    };

    fetchOrders();
  }, [user.id]);

  // If loading
  if (loading) {
    return <div className="text-center mt-10"><Spin /></div>;
  }

  // If error
  if (error) {
    return <div className="text-red-500 text-center mt-10">Error: {error}</div>;
  }

  return (
    <div className="relative overflow-x-auto shadow-md sm:rounded-lg mt-6 sm:mt-8 container mx-auto px-4 sm:px-6 lg:px-8">
      <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" className="px-6 py-3">Mã đơn hàng</th>
            <th scope="col" className="px-6 py-3">Tổng đơn hàng</th>
            <th scope="col" className="px-6 py-3">Trạng thái</th>
            <th scope="col" className="px-6 py-3">Ngày lên đơn</th>
            <th scope="col" className="px-6 py-3">Hành động</th>
          </tr>
        </thead>
        <tbody>
          {orders.map((order) => (
            <tr key={order.id} className="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
              <td className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {order.id}
              </td>
              <td className="px-6 py-4 text-green-600 dark:text-green-400">
                ${order.total_amount}
              </td>
              <td className="px-6 py-4 inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                {order.name_status}
              </td>
              <td className="px-6 py-4 text-gray-900 dark:text-white">
                {formatDate(order.created_at)}
              </td>
              <td className="px-6 py-4">
                <Button
                  type="danger"
                  className="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white border-red-600 border-2 hover:border-red-700 transition-all duration-300 mb-2"
                  onClick={() => cancelOrder(order.id)}
                >
                  Hủy đơn hàng
                </Button>
                {/* Remove duplicate product IDs using Set */}
                {order.products
                  .map(product => product.id) // Get the product IDs
                  .filter((value, index, self) => self.indexOf(value) === index) // Remove duplicates
                  .map(productId => {
                    const product = order.products.find(p => p.id === productId);
                    return (
                      <Link key={productId} href={route('products.show', { id: product.id })}>
                        <Button type="primary" icon={<EyeOutlined />}>
                          Xem chi tiết
                        </Button>
                      </Link>
                    );
                  })}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default OrderHistory;
