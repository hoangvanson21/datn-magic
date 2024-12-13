import { useForm, Head } from '@inertiajs/react';
import { Form, Input, Button, Typography, message } from 'antd';
import { MailOutlined, LockOutlined } from '@ant-design/icons';
import GuestLayout from '@/Layouts/GuestLayout';
import axios from 'axios';

const { Title } = Typography;

export default function ResetPassword({ token, email, isReset }) {
    const { data, setData, processing, errors, reset } = useForm({
        token: token,
        email: email,
        old_password: '',
        password: '',
        password_confirmation: '',
    });

    const submit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post(route('new.password'), {
                token: data.token,
                email: data.email,
                old_password: isReset ? null : data.old_password, // Nếu là reset, không gửi mật khẩu cũ
                password: data.password,
                password_confirmation: data.password_confirmation,
                isReset: isReset,
            });
            message.success(response.data.message);
            reset('old_password', 'password', 'password_confirmation');
        } catch (error) {
            if (error.response && error.response.data) {
                message.error(error.response.data.message || 'Đã xảy ra lỗi');
            } else {
                message.error('Đã xảy ra lỗi');
            }
        }
    };

    return (
        <GuestLayout>
            <Head title={isReset ? 'Đặt lại mật khẩu' : 'Đổi mật khẩu'} />
            <div className="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
                <Title level={2} className="text-center">
                    {isReset ? 'Đặt lại mật khẩu' : 'Đổi mật khẩu'}
                </Title>

                <Form layout="vertical" onSubmitCapture={submit}>
                    <Form.Item
                        label="Email"
                        validateStatus={errors.email && 'error'}
                        help={errors.email}
                    >
                        <Input
                            prefix={<MailOutlined />}
                            type="email"
                            name="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            required
                        />
                    </Form.Item>

                    {isReset ? (
                        // Nếu là đặt lại mật khẩu, không yêu cầu mật khẩu cũ
                        <>
                            <Form.Item
                                label="Mật khẩu mới"
                                validateStatus={errors.password && 'error'}
                                help={errors.password}
                            >
                                <Input.Password
                                    prefix={<LockOutlined />}
                                    name="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    required
                                />
                            </Form.Item>

                            <Form.Item
                                label="Xác nhận mật khẩu mới"
                                validateStatus={errors.password_confirmation && 'error'}
                                help={errors.password_confirmation}
                            >
                                <Input.Password
                                    prefix={<LockOutlined />}
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    onChange={(e) =>
                                        setData('password_confirmation', e.target.value)
                                    }
                                    required
                                />
                            </Form.Item>
                        </>
                    ) : (
                        // Nếu là đổi mật khẩu, yêu cầu mật khẩu cũ
                        <>
                            <Form.Item
                                label="Mật khẩu cũ"
                                validateStatus={errors.old_password && 'error'}
                                help={errors.old_password}
                            >
                                <Input.Password
                                    prefix={<LockOutlined />}
                                    name="old_password"
                                    value={data.old_password}
                                    onChange={(e) => setData('old_password', e.target.value)}
                                    required
                                />
                            </Form.Item>

                            <Form.Item
                                label="Mật khẩu mới"
                                validateStatus={errors.password && 'error'}
                                help={errors.password}
                            >
                                <Input.Password
                                    prefix={<LockOutlined />}
                                    name="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    required
                                />
                            </Form.Item>

                            <Form.Item
                                label="Xác nhận mật khẩu mới"
                                validateStatus={errors.password_confirmation && 'error'}
                                help={errors.password_confirmation}
                            >
                                <Input.Password
                                    prefix={<LockOutlined />}
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    onChange={(e) =>
                                        setData('password_confirmation', e.target.value)
                                    }
                                    required
                                />
                            </Form.Item>
                        </>
                    )}

                    <Form.Item>
                        <Button type="primary" htmlType="submit" loading={processing}>
                            {isReset ? 'Đặt lại mật khẩu' : 'Đổi mật khẩu'}
                        </Button>
                    </Form.Item>
                </Form>
            </div>
        </GuestLayout>
    );
}
