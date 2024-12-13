import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { UserOutlined, MailOutlined, PhoneOutlined, HomeOutlined, EditOutlined, SaveOutlined } from '@ant-design/icons';

export default function Dashboard() {
    const { props } = usePage();
    const user = props.auth.user;
    const address = props.address; // Sử dụng đúng dữ liệu `address` từ props
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({
        name: user.name,
        email: user.email,
        status: user.status,
        phone: address?.phone || '', // Thêm dữ liệu địa chỉ vào formData
        street: address?.street || '', // Thêm trường street vào formData
    });
    const [error, setError] = useState(null);
    const [successMessage, setSuccessMessage] = useState(null);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!formData.name || !formData.email || !formData.status) {
            alert('Vui lòng điền đầy đủ thông tin!');
            return;
        }

        try {
            const response = await axios.patch(route('profile.update'), formData);
            if (response.status === 200) {
                setSuccessMessage(response.data.message);
                setError(null);
                setIsEditing(false);

                window.location.reload();
            }
        } catch (err) {
            console.error('Error:', err);
            setError('Lỗi cập nhật thông tin.');
            setSuccessMessage(null);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard Quản Lý" />
            <section className="bg-gray-100 py-5">
                <div className="container mx-auto">
                    <div className="flex flex-wrap justify-between">
                        {/* Sidebar */}
                        <div className="lg:w-1/3 mb-4 lg:mb-0">
                            <div className="bg-white shadow-lg rounded-lg mb-4 p-4 text-center">
                                <img
                                    src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp"
                                    alt="avatar"
                                    className="rounded-full w-36 mx-auto mb-4"
                                />
                                <h5 className="text-xl font-semibold">{user.name}</h5>
                                <p className="text-gray-500 mb-2">{user.email}</p>
                                <p className="text-gray-400 mb-4">{user.status}</p>
                            </div>
                        </div>

                        {/* Main content */}
                        <div className="lg:w-2/3">
                            <div className="bg-white shadow-lg rounded-lg p-6">
                                <div className="space-y-4">
                                    {/* Full Name */}
                                    <div className="flex justify-between items-center">
                                        <p className="font-semibold text-gray-700 flex items-center">
                                            <UserOutlined className="mr-2" /> Họ và tên
                                        </p>
                                        {isEditing ? (
                                            <input
                                                type="text"
                                                name="name"
                                                value={formData.name}
                                                onChange={handleChange}
                                                className="text-gray-500 p-2 border border-gray-300 rounded"
                                            />
                                        ) : (
                                            <p className="text-gray-500">{user.name}</p>
                                        )}
                                    </div>
                                    <hr />

                                    {/* Email */}
                                    <div className="flex justify-between items-center">
                                        <p className="font-semibold text-gray-700 flex items-center">
                                            <MailOutlined className="mr-2" /> Email
                                        </p>
                                        {isEditing ? (
                                            <input
                                                type="email"
                                                name="email"
                                                value={formData.email}
                                                onChange={handleChange}
                                                className="text-gray-500 p-2 border border-gray-300 rounded"
                                            />
                                        ) : (
                                            <p className="text-gray-500">{user.email}</p>
                                        )}
                                    </div>
                                    <hr />

                                    {/* Phone */}
                                    <div className="flex justify-between items-center">
                                        <p className="font-semibold text-gray-700 flex items-center">
                                            <PhoneOutlined className="mr-2" /> Số điện thoại
                                        </p>
                                        {isEditing ? (
                                            <input
                                                type="text"
                                                name="phone"
                                                value={formData.phone}
                                                onChange={handleChange}
                                                className="text-gray-500 p-2 border border-gray-300 rounded"
                                            />
                                        ) : (
                                            <p className="text-gray-500">{address?.phone}</p>
                                        )}
                                    </div>
                                    <hr />

                                    {/* Street */}
                                    <div className="flex justify-between items-center">
                                        <p className="font-semibold text-gray-700 flex items-center">
                                            <HomeOutlined className="mr-2" /> Địa chỉ
                                        </p>
                                        {isEditing ? (
                                            <input
                                                type="text"
                                                name="street"
                                                value={formData.street}
                                                onChange={handleChange}
                                                className="text-gray-500 p-2 border border-gray-300 rounded"
                                            />
                                        ) : (
                                            <p className="text-gray-500">{address?.street}</p>
                                        )}
                                    </div>

                                    {/* Edit Button */}
                                    <div className="mt-4 flex justify-end space-x-4">
                                        {isEditing ? (
                                            <button
                                                onClick={handleSubmit}
                                                className="px-4 py-2 bg-blue-500 text-white rounded flex items-center"
                                            >
                                                <SaveOutlined className="mr-2" /> Lưu thay đổi
                                            </button>
                                        ) : (
                                            <button
                                                onClick={() => setIsEditing(true)}
                                                className="px-4 py-2 bg-yellow-500 text-white rounded flex items-center"
                                            >
                                                <EditOutlined className="mr-2" /> Chỉnh sửa
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </AuthenticatedLayout>
    );
}
